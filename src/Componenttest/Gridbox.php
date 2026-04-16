<?php
namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class Gridbox extends Component
{
    protected $properties = [
        'content' => [
            'selector',
            'left',
            'right',
        ],
    ];

    /**
     * Returns markup.
     *
     * @param none
     * @return string
     */
    protected function markup()
    {
        $m = [];

        $this->prependClass('container');

        // Set anchor id
        $selector           = $this->co('selector');
        $selectorAttributes = $selector->getAttributes();
        if (isset($selectorAttributes['anchor'])) {
            $this->setAttribute('id', $selectorAttributes['anchor']);
        }

        $m[] = '<div ' . $this->htmlAttributes() . '>';
        $m[] = '<div class="row">';
        $m[] = '<div class="col-6" data-content="left">' . $this->getContentMarkupByKey('left') . '</div>';
        $m[] = '<div class="col-6" data-content="right">' . $this->getContentMarkupByKey('right') . '</div>';
        $m[] = '</div>'; // row
        $m[] = '</div>'; // container
        return implode("", $m);
    }
}
