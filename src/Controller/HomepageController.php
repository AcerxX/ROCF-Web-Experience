<?php

namespace App\Controller;

use App\Security\WebserviceUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    public function homepage()
    {
        /** @var WebserviceUser $user */
        $user = $this->getUser();

        $username = 'NOT LOGGED IN';
        if ($user !== null) {
            $username = $user->getFirstName() . ' ' . $user->getLastName();
        }

        return $this->render(
            'homepage.html.twig',
            [
                'user' => $user
            ]
        );
    }

    public function perks()
    {
        return $this->render('perks.html.twig');
    }
}
