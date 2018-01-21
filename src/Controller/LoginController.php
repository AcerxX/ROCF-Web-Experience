<?php

namespace App\Controller;


use App\Service\ApiService;
use function setcookie;
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

            $requestBag = [
                'email' => $email,
                'password' => $password,
                'ipAddress' => $ipAddress,
                'locale' => $request->getLocale()
            ];

            try {
                $response = $apiService->callUsersEngineApi(ApiService::ROUTE_UE_LOGIN, $requestBag);
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

    public function forgot_password(Request $request, ApiService $apiService)
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

