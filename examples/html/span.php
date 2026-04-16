<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\FragmentFactory;

// Attributes as root key
// Works aswell!
$tag = FragmentFactory::make('html.a', [
  'id' => 'test',
  'class' => 'sdkjkl',
  'href' => 'myurl.html',
  'target' => '_blank',
  'title' => 'hello!',
  'content' => 'Click me!',
  'content' => FragmentFactory::make('html.span', [
    'content' => 'Whooho!',
    'data-say' => 'what'
  ])
]);

$markup = $tag->render();
echo showHTMLCode($markup);