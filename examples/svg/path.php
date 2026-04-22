<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\Factory;

// Works aswell: attributes as root key
$tag = Factory::make('fragment.svg.path', [
    'id'        => 'test',
    'class'     => 'test',
    'something' => 'else',
    'd'         => 'M 10,30
           A 20,20 0,0,1 50,30
           A 20,20 0,0,1 90,30
           Q 90,60 50,90
           Q 10,60 10,30 z',
]);

echo showHTMLCode($tag->render());
