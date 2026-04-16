<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use Gajus\Dindent\Indenter;
use wwaz\Components\Factory;

// Construct by real classname
$component = Factory::make('wwaz.Components.Componenttest.Callaction', [
  'class' => 'callaction-inverse',
  'text' => 'Click me!'
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));
