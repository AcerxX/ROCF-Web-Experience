<?php

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    public function login(AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastEmail = $authUtils->getLastUsername();

        return $this->render(
            'login.html.twig',
            [
                'errorMessage' => $error,
                'lastEmail' => $lastEmail
            ]
        );
    }

    public function register(Request $request, ApiService $apiService)
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
                    return $this->redirectToRoute('login');
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

    public function resetPassword(Request $request, ApiService $apiService)
    {
        $errorMessage = '';
        $token = $request->get('token');

        if ($token === null) {
            return $this->redirectToRoute('homepage');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $userInformation = $apiService->callUsersEngineApi(
                    ApiService::ROUTE_UE_CHECK_TOKEN,
                    [
                        'token' => $token
                    ]
                );

                if ($userInformation['isError']) {
                    $errorMessage .= 'Tokenul a expirat. Va rugam sa folositi din nou optiunea de resetare parola.';
                } else {
                    $resetPasswordResponse = $apiService->callUsersEngineApi(
                        ApiService::ROUTE_UE_EDIT_PROFILE,
                        [
                            'email' => $userInformation['email'],
                            'changes' => [
                                'password' => $request->request->get('pass')
                            ]
                        ]
                    );

                    if ($resetPasswordResponse['isError']) {
                        $errorMessage .= 'A aparut o eroare la schimbarea datelor. Va rugam sa reincercati!';
                    } else {
                        $this->redirectToRoute('login');
                    }
                }
            } catch (\Exception $e) {
                $errorMessage .= $e->getMessage();
            }
        }

        return $this->render(
            'resetPassword.html.twig',
            [
                'token' => $token,
                'errorMessage' => $errorMessage
            ]
        );
    }
}
