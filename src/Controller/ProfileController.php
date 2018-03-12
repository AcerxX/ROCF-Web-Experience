<?php
/**
 * Created by PhpStorm.
 * User: catal
 * Date: 05-Mar-18
 * Time: 18:41
 */

namespace App\Controller;

use App\Security\WebserviceUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{

    public function profile(){
        $user = $this->getUser();
        return $this->render(
            'profile.html.twig',
            [
                'user' => $user
            ]
        );
    }
}