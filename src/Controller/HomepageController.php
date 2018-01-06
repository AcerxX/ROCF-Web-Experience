<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    public function homepage()
    {
        return $this->render(
            'homepage.html.twig'
        );
    }
}