<?php

namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class BannerHero extends Component
{
    protected $wrapTag = 'section';

    protected $properties = [
      'content' => [
        'headline',
        'subline',
        'callaction',
        'image'
      ]
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
        $m[] = '<div class="fullsize-image"' . $this->getBackgroundImageStyle() . '>';
        $m[] = '<h1>' . $this->cr('headline') . '</h1>';
        $m[] = '<p>' . $this->cr('subline') . '</p>';
        $m[] = $this->cr('callaction');
        $m[] = '</div>';
        return implode("", $m);
    }

    protected function getBackgroundImageStyle()
    {
        $bgurl = '';
        if ($image = $this->co('image')) {
            $large = $image->getFormat('large');
            if ($large) {
                $bgurl = ' style="background-image: url(public/assets/images/' . $large['name'] .');"';
            }
        }
        return $bgurl;
    }
}
