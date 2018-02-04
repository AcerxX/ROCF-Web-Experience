<?php

namespace App\Controller;

use App\Exception\RecaptchaException;
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

    public function forgotPassword(Request $request, ApiService $apiService)
    {
        $successMessage = '';
        $errorMessage = '';

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $apiService->checkRecaptcha(
                    $request->request->get('g-recaptcha-response'),
                    $request->server->get('SERVER_ADDR')
                );

                $requestBag = ['email' => $request->request->get('recovery_email')];
                $response = $apiService->callUsersEngineApi(ApiService::ROUTE_UE_RESET_PASSWORD, $requestBag);

                if ($response['isError'] === false) {
                    $successMessage = 'reset_password.success';
                } else {
                    $errorMessage = 'reset_password.error';
                }
            } catch (\InvalidArgumentException $e) {
                $this->get('logger')->critical($e->getMessage());
                $errorMessage = 'A aparut o eroare, va rugam reincercati!';
            } catch (RecaptchaException $e) {
                $errorMessage = $e->getMessage();
            }
        }

        return $this->render(
            'forgot_password.html.twig',
            [
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage
            ]
        );
    }
}
