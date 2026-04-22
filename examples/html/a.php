<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\Factory;

// Well defined.
$tag = Factory::make('fragment.html.a', [
    'attributes' => [
        'id'     => 'test',
        'class'  => 'sdkjkl',
        'href'   => 'myurl.html',
        'target' => '_blank',
        'title'  => 'hello!',
    ],
    'content'    => Factory::make('fragment.html.span', [
        'content'    => 'Whooho!',
        'attributes' => [
            'data-say' => 'what',
        ],
    ]),
]);

$markup = $tag->render();
echo showHTMLCode($markup);

// Works aswell: Attributes as root key
$tag = Factory::make('fragment.html.a', [
    'id'      => 'test',
    'class'   => 'sdkjkl',
    'href'    => 'myurl.html',
    'target'  => '_blank',
    'title'   => 'hello!',
    'content' => 'Click me!',
    'content' => Factory::make('fragment.html.span', [
        'content'  => 'Whooho!',
        'data-say' => 'what',
    ]),
]);

$markup = $tag->render();
echo showHTMLCode($markup);
