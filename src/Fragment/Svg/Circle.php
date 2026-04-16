<?php

namespace wwaz\Components\Fragment\Svg;

class Circle extends SvgTag
{
    protected $isContainer = false;

    protected $properties = [
      'attributes' => [
        'cx' => 'required|isInt',
        'cy' => 'required|isInt',
        'r' => 'required|isInt'
      ]
    ];

}
