<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\Factory;

$tag = Factory::make('fragment.html.div', [
  'href' => 'myurl.html',
  'target' => '_blank',
  'title' => 'hello!',
  'content' => 'Click me!'
]);

$markup = $tag->render();
echo showHTMLCode($markup);
