<?php

namespace wwaz\Components\Fragment\Html;

use wwaz\Components\Fragment\Xml\XmlTag;

abstract class HtmlTag extends XmlTag
{
    protected $isContainer;

    protected $properties = [
        'attributes' => [
            'id'    => 'isString',
            'class' => 'isString',
        ],
    ];

    public function __construct($data)
    {
        $data['tag']         = $this->getTag();
        $data['isContainer'] = $this->isContainer;
        parent::__construct($data);
    }

    public function isContainer()
    {
        return $this->isContainer;
    }

    public function getTag()
    {
        $classname = get_class($this);
        $classname = explode('\\', $classname);
        return strtolower($classname[count($classname) - 1]);
    }

    protected function markup()
    {

        $m = '<' . $this->getTag();

        if ($attributes = $this->htmlAttributes()) {
            $m .= $attributes;
        }

        if ($this->isContainer()) {

            $m .= '>';

            if ($contains = $this->getContent()) {
                $m .= $this->getContentMarkup($contains);
            }

            $m .= '</' . $this->getTag() . '>';

        } else {
            $m .= ' />';
        }
        return $m;
    }

    // public function render(){
    //   return $this->markup();
    // }

}
