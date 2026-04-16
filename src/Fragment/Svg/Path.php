<?php

namespace wwaz\Components\Fragment\Svg;

class Path extends SvgTag
{
    protected $isContainer = false;

    protected $properties = [
      'attributes' => [
        'd' => 'required|isString'
      ]
    ];

}
