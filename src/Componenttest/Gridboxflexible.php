<?php

namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class Gridboxflexible extends Component
{
    protected $properties = [
        'data'    => [
            'columns' => 'isInt|required|min:1|max:6',
        ],
        'content' => [
            'cards',
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

        $m[] = '<div' . $this->htmlAttributes() . '>';
        $m[] = $this->renderColumns();
        $m[] = '</div>'; // container
        return implode('', $m);

    }

    protected function renderColumns()
    {
        $cards = $this->co('cards');

        if (! is_array($cards)) {
            return '';
        }

        $columnsPerRow = $this->getData('columns');
        $rowsTotal     = ceil(count($cards) / $columnsPerRow);

        $colclass = 'col-' . floor(12 / $columnsPerRow);

        $m = [];

        $cardCounter = 0;

        for ($i = 0; $i < $rowsTotal; $i++) {
            $m[] = '<div class="row">';
            for ($j = 0; $j < $columnsPerRow; $j++) {
                $m[] = '<div class="' . $colclass . '">';
                if (isset($cards[$cardCounter])) {
                    $m[] = $cards[$cardCounter]->render();
                    $cardCounter++;
                }
                $m[] = '</div>';
            }
            $m[] = '</div>';
        }

        return implode('', $m);
    }
}
