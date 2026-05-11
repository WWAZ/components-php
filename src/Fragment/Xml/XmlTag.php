<?php

namespace wwaz\Components\Fragment\Xml;

use wwaz\Components\BaseComponent;

class XmlTag extends BaseComponent
{
    protected $tag;

    protected $isContainer;

    protected $properties = [
        'attributes' => 'arrayVal',
        'content'    => 'arrayVal',
    ];

    public function __construct($data)
    {
        $this->mergeProperties();
        $this->tag         = $data['tag'];
        $this->isContainer = $data['isContainer'];
        unset($data['tag']);
        unset($data['isContainer']);
        $this->data = $this->validateData($data);
    }

    public function isContainer()
    {
        return $this->isContainer;
    }

    public function addContent($content)
    {
        $this->data['content'][] = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->data['content'];
    }

    /**
     * Sets attribute.
     *
     * @param string name
     * @param mixed $value
     * @return self
     */
    public function setAttribute($name, $value)
    {
        $this->data['attributes'][$name] = $value;
        return $this;
    }

    /**
     * Returns attribute.
     *
     * @param string name
     * @param mixed|null
     */
    public function getAttribute($name)
    {
        if (isset($this->data['attributes'][$name])) {
            return $this->data['attributes'][$name];
        }
        return null;
    }

    /**
     * Returns all attributes.
     *
     * @param string name
     * @param array|null
     */
    public function getAttributes()
    {
        return isset($this->data['attributes']) ? $this->data['attributes'] : [];
    }

    /**
     * Returns attributes as html markup.
     *
     * @param none
     * @return string
     */
    protected function markupAttributes()
    {

        $m = [];

        $attributes = $this->getAttributes();

        if (empty($attributes)) {
            return '';
        }

        foreach ($attributes as $key => $value) {
            if (! is_null($value)) {
                if (! is_array($value)) {
                    $m[] = $key . '="' . $value . '"';
                } else {
                    // e.g. case class names [cn1, cn2 ...]
                    $m[] = $key . '="' . implode(' ', $value) . '"';
                }
            }
        }

        if (! empty($m)) {
            return ' ' . implode(' ', $m);
        }

        return '';

    }

    public function setTag($name)
    {
        $this->tag = $name;
    }

    public function getTag()
    {
        return strtolower($this->tag);
    }

    public function markup(): string|bool
    {

        $m = '<' . $this->getTag();

        $m .= $this->markupAttributes();

        if ($this->data['isContainer']) {

            $m .= '>';

            if ($content = $this->getContent()) {
                $m .= $this->getContentMarkup($content);
            }

            $m .= '</' . $this->getTag() . '>';

        } else {
            $m .= ' />';
        }
        return $m;
    }

    public function toXml()
    {
        return $this->markup();
    }

    /**
     * Returns markup of all (and nested)
     * content elements of given component array.
     *
     * @recursive
     * @param array
     * @return string
     */
    protected function getContentMarkup($content)
    {

        if (! $content) {
            return '';
        }

        $m = '';

        if (is_array($content)) {

            for ($i = 0; $i < count($content); $i++) {
                $m .= $this->getContentMarkup($content[$i]);
            }

        } else {

            if ($this->isTagObject($content)) {
                // Another component
                $m .= $content->markup();
            } else {
                $m .= $content;
            }
        }
        return $m;
    }

    /**
     * Returns true when given variable
     * is a Component Object.
     *
     * @param mixed $var
     * @return boolean
     *
     * Get all subclasses: @see https://stackoverflow.com/questions/3470008/how-to-obtain-all-subclasses-of-a-class-in-php
     */
    protected function isTagObject($var)
    {
        if (is_object($var)) { // && is_subclass_of($var, __NAMESPACE__ . '\Tag')
            return true;
        }
        return false;
    }
}
