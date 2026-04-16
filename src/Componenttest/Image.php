<?php

namespace wwaz\Components\Componenttest;

use wwaz\Components\Component;
use wwaz\Components\Config;

class Image extends Component
{
    protected $properties = [
      'attributes' => [
        'name' => '*',
        'hash' => '*',
        'ext' => '*',
        'mime' => '*',
        'width' => 'isInt|required',
        'height' => 'isInt|required',
        'size' => '*',
        'path' => '*',
        'url' => '*',
        'alternativeText' => '*',
        'formats' => '*'
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
        $m = '<img';
        $m .= ' src="' . $this->getSrc() . '"';
        $m .= ' width="' . $this->getAttribute('width') . '"';
        $m .= ' height="'.$this->getAttribute('height').'"';
        $m .= ' alt="'.$this->getAttribute('alternativeText').'"';
        $m .= '/>';
        return $m;
    }

    public function getFormats()
    {
        return $this->getAttribute('formats');
    }

    public function getFormat($name)
    {
        $formats = $this->getFormats();
        if (isset($formats[$name])) {
            return $formats[$name];
        }
        return null;
    }

    public function getSrc()
    {
        $src = $this->getAttribute('name');
        $url = Config::get(get_class($this), 'url.images');
        if ($url) {
            $src = $url . '/' . $src;
        }
        return $src;
    }
}
