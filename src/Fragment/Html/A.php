<?php

namespace wwaz\Components\Fragment\Html;

class A extends HtmlTag
{
    protected $isContainer = true;

    protected $properties = [
      'attributes' => [
        'href' => 'required',
        'target' => 'select:null,_blank,_parent,_self,_top|default:null',
        'title' => 'isString|length:1,255',
      ]
    ];

}
