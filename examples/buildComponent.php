<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';
require 'functions.php';

use Gajus\Dindent\Indenter;
use wwaz\Components\Component;

class Card extends Component
{
    protected $properties = [
        'content' => [
            'title' => 'required|isString',
            'text'  => 'required|isString',
        ],
    ];

    protected function markup(): string
    {
        return '
            <h2 class="card__title">' . $this->content('title') . '</h2>
            <p class="card__text">' . $this->content('text') . '</p>
        ';
    }
}

$component = new Card([
    'title' => 'My Card',
    'text'  => 'My text',
]);

$markup = $component->render();
echo showHTMLCode((new Indenter())->indent($markup));
