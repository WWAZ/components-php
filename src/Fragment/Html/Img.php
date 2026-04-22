<?php

namespace wwaz\Components\Fragment\Html;

class Img extends HtmlTag
{
    protected $isContainer = false;

    protected $properties = [
      'attributes' => [
        'src' => 'required|isString',
        'alt' => 'recommended|isString|length:1,255',
        'width' => 'recommended',
        'height' => 'recommended',
      ],
    ];

}
