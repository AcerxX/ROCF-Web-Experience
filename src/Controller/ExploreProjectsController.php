<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExploreProjectsController extends Controller
{
    public function explore()
    {
        /** @var WebserviceUser $user */
        $user = $this->getUser();

        $username = 'NOT LOGGED IN';
        if ($user !== null) {
            $username = $user->getFirstName() . ' ' . $user->getLastName();
        }

        return $this->render(
            'explore.html.twig',
            [
                'user' => $user
            ]
        );
    }
}