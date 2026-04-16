<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use wwaz\Components\Factory;
use Gajus\Dindent\Indenter;

$component = Factory::make('componenttest.gridbox', [
  'id' => 'gridbox',
  'class' => 'my-gridbox-component',
  'selector' => Factory::make('componenttest.selector', [
    'id' => 1,
    'class' => '',
    'anchor' => 'cerv-programm'
  ]),
  'left' => Factory::make('fragment.html.span', [
    'content' => 'Hello left!'
  ]),
  'right' => Factory::make('componenttest.callaction', [
    'text' => 'Hello right!'
  ])
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));