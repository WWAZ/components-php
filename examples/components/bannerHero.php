<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../vendor/autoload.php';
require '../functions.php';

use Gajus\Dindent\Indenter;
use wwaz\Components\Factory;
use wwaz\Components\Config;

// Set url.images for namespace wwaz\\Components\\Componenttest
Config::set('wwaz\\Components\\Componenttest', [
  'wrapComponent' => [
    'class' => 'component test-component'
  ],
  'url' => [
    'images' => 'public/assets/images'
  ]
]);

// Build component
$component = Factory::make('componenttest.bannerHero', [
  'headline' => 'Lorem ipsum dolor sit amet',
  'subline' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  'callaction' => Factory::make('componenttest.callaction', [
    'href' => '#cerv-programm',
    'text' => 'Förderbare Aktionsbereiche'
  ]),
  'image' => Factory::make('componenttest.image', [
    'id' => 15,
    'name' => 'beautyful-image.jpg',
    'alternativeText' => '',
    'caption' => '',
    'width' => 1920,
    'height' => 1280,
    'formats' => [
      'thumbnail' => [
        'name' => 'thumbnail_beautyful-image.jpg',
        'hash' => 'thumbnail_beautyful-image_23342af34f',
        'ext' => '.jpg',
        'mime' => 'image/jpeg',
        'width' => 234,
        'height' => 156,
        'size' => 9.64,
        'path' => null,
        'url' => '/uploads/thumbnail_beautyful-image_23342af34f.jpg'
      ],
      'large' => [
        'name' => 'large_beautyful-image.jpg',
        'hash' => 'large_beautyful-image_23342af34f',
        'ext' => '.jpg',
        'mime' => 'image/jpeg',
        'width' => 1000,
        'height' => 667,
        'size' => 95.65,
        'path' => null,
        'url' => '/uploads/large_beautyful-image_23342af34f.jpg'
      ]
    ]
  ])
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));