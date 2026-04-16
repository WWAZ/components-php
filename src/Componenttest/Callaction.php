<?php
namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class Callaction extends Component
{
    protected $properties = [
        'content' => [
            'text',
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
        $this->prependClass('callaction');
        return '<a' . $this->htmlAttributes() . '>' . $this->getContentMarkupByKey('text') . '</a>';
    }
}
