<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 06.01.2018
 * Time: 16:40
 */

namespace App\Controller;


use App\Service\ApiService;
use App\Service\SupermanService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SupermanController extends Controller
{
    public function superman(SupermanService $supermanService, ApiService $apiService)
    {
        $imagePath = $supermanService->computeImagePath();

        // Presupunand ca avem deja format un request cu datele introduse in formular de user de forma de mai jos
        $request = [
            'email' => 'al3x1394@gmail.com',
            'password' => '#Adgjmptx1313',
            'firstName' => 'Alexandru',
            'lastName' => 'Mihai',
            'ipAddress' => '5.12.56.76',
            'locale' => 'ro'
        ];

        /**
         * Se apeleaza metoda callUsersEngineApi cu ruta pe care o vrem
         * (se vor folosi constantele definite in ApiService) si cu requestul format.
         *
         * Raspunsul va fi stocat in variabila response si va fi sub forma de array.
         *
         * Pentru a vedea un exemplu cu ce contine variabila response intrati pe ruta lui superman.
         */
        try {
            $response = $apiService->callUsersEngineApi(ApiService::ROUTE_UE_REGISTER, $request);
        } catch (\InvalidArgumentException $e) {
            $this->get('logger')->critical($e->getMessage());
        }

        var_dump($response); die();

        return $this->render(
            'number.html.twig',
            [
                'IMAGE_PATH' => $imagePath
            ]
        );
    }
}