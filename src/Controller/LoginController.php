<?php
/**
 * Created by PhpStorm.
 * User: Skartzone
 * Date: 1/7/2018
 * Time: 10:37 AM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $error = '';
        $submitAction = $request->get('submitAction');
        if (!empty($submitAction)) {
            $email = trim($request->get('email'));
            $password = $request->get('password');
            $ipAddress = $request->getClientIp();
            //verificat ca email si pass nu sunt empty. empty($var)

            if (empty($email)) {
                $error .= 'Va rog introduceti adresa de e-mail';
            }
            if (empty($password)) {
                $error .= 'Va rog introduceti parola';
            }
            if (empty($error)) {
                //api
                $response = '{"isError":false,"errorMessage":[],"userInformation":{"userId":1,"firstName":"Alexandru","lastName":"Mihai","role":"SUPERUSER"}}';
                $response = json_decode($response);
                if ($response['isError'] === false) {
                    setcookie('thorocea',$response['userId'].'_'.$ipAddress);
                    $this->redirectToRoute('homepage');
                }
                $error .= $response['errorMessage'];


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
