<?php
/**
 * Created by PhpStorm.
 * User: Skartzone
 * Date: 1/7/2018
 * Time: 10:37 AM
 */

namespace App\Controller;


use App\Service\ApiService;
use function setcookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use function var_dump;

class LoginController extends Controller
{
    public function login ()
    {
        return $this->render('login.html.twig', []);
    }

    public function register (Request $request, ApiService $apiService)
    {
        $error = '';
        $requestBag = [];

        if ($request->getMethod() === Request::METHOD_POST) {
            $requestBag = [
                'email' => $request->request->get('email'),
                'password' => $request->request->get('pass'),
                'firstName' => $request->request->get('first_name'),
                'lastName' => $request->request->get('last_name'),
                'ipAddress' => $request->getClientIp(),
                'locale' => $request->getLocale()
            ];

            try {
                $response = $apiService->callUsersEngineApi(ApiService::ROUTE_UE_REGISTER, $requestBag);

                if ($response['isError'] === false) {
                    setcookie(
                        'thorocea',
                        $response['userInformation']['userId'] . '_' . $request->getClientIp(),
                        3600 * 24
                    // TO DO secure true
                    );
                    return $this->redirectToRoute('homepage');
                }

                $error = $response['errorMessage'];
            } catch (\InvalidArgumentException $e) {
                $this->get('logger')->critical($e->getMessage());
                $error = 'A aparut o eroare, va rugam reincercati!';
            }
        }

        return $this->render(
            'register.html.twig',
            [
                'error' => $error,
                'predefinedValues' => $requestBag
            ]
        );
    }
}