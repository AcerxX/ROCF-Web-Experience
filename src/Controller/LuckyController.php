<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LuckyController extends Controller
{
    public function superman()
    {
        return $this->render('number.html.twig', []);
    }
}