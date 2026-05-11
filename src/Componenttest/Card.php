<?php

namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class Card extends Component
{
    protected $properties = [
        'content' => [
            'title',
            'text',
            'subline',
            'icon',
            'callaction',
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
        $m = [];

        // $this->prependClass('container');
        //
        // // Set anchor id
        // $selector = $this->co('selector');
        // $selectorAttributes = $selector->getAttributes();
        // if( isset($selectorAttributes['anchor']) ){
        //   $this->setAttribute('id', $selectorAttributes['anchor']);
        // }

        $m[] = '<div' . $this->htmlAttributes() . '>';

        $icon = $this->cr('icon');
        if ($icon) {
            $m[] = $icon;
        }

        $m[] = '<h3>' . $this->cr('title') . '</h3>';

        $subline = $this->cr('subline');
        if ($subline) {
            $m[] = '<p class="subline"><strong>' . $subline . '</strong><p>';
        }

        $m[] = '<p>' . $this->cr('text') . '</p>';

        $callaction = $this->cr('callaction');
        if ($callaction) {
            $m[] = $callaction;
        }

        $m[] = '</div>';

        return implode('', $m);
    }
}
