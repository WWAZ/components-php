<?php

namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class Selector extends Component
{
    protected $properties = [
        'attributes' => [
            'anchor' => '*',
        ],
    ];

    /**
     * Returns markup.
     *
     * @param none
     * @return string
     */
    public function markup(): string
    {
        return $this->getAttributes();
    }
}
