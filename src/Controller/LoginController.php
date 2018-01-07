<?php
/**
 * Created by PhpStorm.
 * User: Skartzone
 * Date: 1/7/2018
 * Time: 10:37 AM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    public function login ()
    {
        return $this->render('login.html.twig', []);
    }
}