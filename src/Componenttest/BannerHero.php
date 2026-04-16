<?php
namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;

class BannerHero extends Component
{
    protected $wrapTag = 'section';

    protected $properties = [
        'content' => [
            'headline' => 'isString',
            'subline' => 'isString',
            'callaction' => 'isComponent',
            'image' => 'isComponent',
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
        $m   = [];
        $m[] = '<div class="fullsize-image"' . $this->getBackgroundImageStyle() . '>';
        $m[] = '<h1>' . $this->content('headline') . '</h1>';
        $m[] = '<p>' . $this->content('subline') . '</p>';
        $m[] = $this->content('callaction');
        $m[] = '</div>';
        return implode("", $m);
    }

    protected function getBackgroundImageStyle()
    {
        $bgurl = '';
        if ($image = $this->object('image')) {
            $large = $image->getFormat('large');
            if ($large) {
                $bgurl = ' style="background-image: url(public/assets/images/' . $large['name'] . ');"';
            }
        }
        return $bgurl;
    }
}
