<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 06.01.2018
 * Time: 16:40
 */

namespace App\Controller;


use App\Service\SupermanService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SupermanController extends Controller
{
    public function superman(SupermanService $supermanService)
    {
        $imagePath = $supermanService->computeImagePath();

        return $this->render(
            'number.html.twig',
            [
                'IMAGE_PATH' => $imagePath
            ]
        );
    }
}