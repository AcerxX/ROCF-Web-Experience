<?php
namespace App\Controller;

use App\Service\MessageGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LuckyController extends Controller
{
    public function number(LoggerInterface $logger)
    {
        $number = -1;
        try {
            $number = random_int(0, 100);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }

        $logger->critical('TEST CRITICAL MESSAGE');

        return $this->render('number.html.twig', array(
            'number' => $number,
        ));
    }


    public function listMessage(LoggerInterface $logger, MessageGenerator $messageGenerator)
    {
        return $this->render(
            'number.html.twig',
            [
                'number' => $messageGenerator->getHappyMessage()
            ]
        );
    }
}