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

    public function forgotPassword(Request $request, ApiService $apiService)
    {
        $error = '';

        /*if ($request->getMethod() === Request::METHOD_POST) {
            $recovery_email = $request->get('recovery_email');
        }

        try {
           $response = $apiService->callUsersEngineApi


        if ($response['isError'] === false) {
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo($recovery_email)
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/registration.html.twig',
                        array('name' => $name)
                    ),
                    'text/html'
                )

            ;

            $mailer->send($message);

            return $this->render(...);
        }
*/

        return $this->render('forgot_password.html.twig');
    }
}
