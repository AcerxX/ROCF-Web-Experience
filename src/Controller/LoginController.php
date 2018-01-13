<?php
/**
 * Created by PhpStorm.
 * User: Skartzone
 * Date: 1/7/2018
 * Time: 10:37 AM
 */

namespace App\Controller;


use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    public function login(Request $request, ApiService $apiService)
    {
        $error = '';
        $submitAction = $request->get('submitAction');
        if (!empty($submitAction)) {
            $email = trim($request->get('email'));
            $password = $request->get('password');
            $ipAddress = $request->getClientIp();
            //verificat ca email si pass nu sunt empty. empty($var)


            $requestbag = [
                'email' => $email,
                'password' => $password,
                'ipAddress' => $ipAddress,
            ];
            try {
                $response = $apiService->callUsersEngineApi(ApiService::ROUTE_UE_LOGIN, $requestbag);
                if ($response['isError'] === false) {
                    setcookie('thorocea', $response['userInformation']['userId'] . '_' . $ipAddress);
                    return $this->redirectToRoute('homepage');
                }

                $error .= $response['errorMessage'];


            } catch (\InvalidArgumentException $e) {
                $this->get('logger')->critical($e->getMessage());
                $error .= 'A aparut o eroare. Va rugam reincercati!';
            }


        }

        return $this->render(
            'login.html.twig',
            [
                'errorMessage' => $error,
                'lastEmail' => $email ?? ''
            ]
        );

    }
}
