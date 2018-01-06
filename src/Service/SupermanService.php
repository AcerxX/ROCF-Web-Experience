<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 06.01.2018
 * Time: 16:51
 */

namespace App\Service;


class SupermanService
{
    private const IMAGE_PATH = 'build/images/poza.png';

    public function computeImagePath(): string
    {
       return self::IMAGE_PATH;
    }
}