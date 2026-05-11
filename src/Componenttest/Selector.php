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
    public function markup(): string|bool
    {
        return $this->getAttributes();
        // $m = [];
        // $m[]= '<div class="container">';
        //   $m[]= '<div class="row">';
        //     $m[]= '<div class="col-6" data-content="left">' . $this->getContentMarkupByKey('left') . '</div>';
        //     $m[]= '<div class="col-6" data-content="right">' . $this->getContentMarkupByKey('right') . '</div>';
        //   $m[]= '</div>'; // row
        // $m[]= '</div>'; // container
        // return implode("", $m);
    }
}
