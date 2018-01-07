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
        $user = $request->get('user');
        $password = $request->get('password');
        $ipAdress = $request->getClientIp();
        return $this->render('login.html.twig', []);
    }
}