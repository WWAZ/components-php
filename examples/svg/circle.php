<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\Factory;

// Works aswell: Attributes as root key
$tag = Factory::make('fragment.svg.circle', [
    'cx' => 10,
    'cy' => 10,
    'r'  => 50,
]);

echo showHTMLCode($tag->render());
