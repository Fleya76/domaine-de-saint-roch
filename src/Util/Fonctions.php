<?php

namespace App\Util;

use Symfony\Component\Dotenv\Dotenv;

class Fonctions 
{

    //Fonction qui permet de récupérer les paramètres de .env
    public static function getEnv($variable){

        $dotenv = new Dotenv();
        $dotenv->load('../.env');
        
        return $_ENV[$variable];

    }
}