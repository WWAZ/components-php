<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use Gajus\Dindent\Indenter;
use wwaz\Components\Factory;

$component = Factory::make('fragment.html.a', [
    'id'      => 'my-link',
    'class'   => 'my-link',
    'href'    => 'my-url.html',
    'target'  => '_blank',
    'title'   => 'my great link',
    'content' => 'Click me!',
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));
