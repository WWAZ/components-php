<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use Gajus\Dindent\Indenter;
use wwaz\Components\Factory;

$component = Factory::make('componenttest.card', [
    'icon'    => Factory::make('componenttest.icon', [
        'id'                => 10,
        'name'              => 'icon-strand-1-colored.svg',
        'alternativeText'   => '',
        'caption'           => '',
        'width'             => 60,
        'height'            => 60,
        'formats'           => null,
        'hash'              => 'icon_strand_1_colored_0483926b8c',
        'ext'               => '.svg',
        'mime'              => 'image/svg+xml',
        'size'              => 2.75,
        'url'               => '/uploads/icon_strand_1_colored_0483926b8c.svg',
        'previewUrl'        => null,
        'provider'          => 'local',
        'provider_metadata' => null,
        'created_at'        => '2021-06-07T15:51:52.000Z',
        'updated_at'        => '2021-06-07T18:51:29.000Z',
    ]),
    'title'   => 'Werte der Union',
    'subline' => '',
    'text'    => 'Schutz und Förderung von Unionswerten.',
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));
