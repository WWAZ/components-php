<?php

namespace wwaz\Components;

use wwaz\Components\Parse\HtmlParser;

abstract class Component extends BaseComponent implements ComponentInterface
{
    /**
     * Wrap tag:
     * Tagname, the component will be wrapped with.
     * e.g. 'section', 'div'
     *
     * @var string
     */
    protected $wrapTag;

    /**
     * Additional component classnames, which may
     * be added to a component in runtime.
     * This may be handy to extend components by css classes.
     * Default is empty.
     *
     * Example:
     * Component class name:    base component accordion
     * Additional class name:   green-titles (Added by $component->addComponentClass('green-titles') in runtime)
     * Markup result:           <div class="base component accordion green-titles"> ... </div>
     *
     * @var array
     */
    protected $componentClasses = [];

    /**
     * Classnames listed here will be removed from
     * default component class list.
     *
     * Example:
     * Component class name:    base component accordion
     * Remove name:             component
     * Markup result:           <div class="base accordion"> ... </div>
     *
     * @var array
     */
    protected $removeComponentClasses = [];

    /**
     * Constructor.
     *
     * @param array $data – property data
     */
    public function __construct($data)
    {
        $this->mergeProperties();
        parent::__construct($data);
    }

    /**
     * Sets wrapping tag.
     *
     * @param string $tag
     * @return nothing
     */
    public function setWrapTag($tag)
    {
        $this->wrapTag = $tag;
    }

    /**
     * Adds an additional classname
     * to the component.
     *
     * @param string $name
     * @return self
     */
    public function addComponentClass($name)
    {
        if (!in_array($name, $this->componentClasses)) {
            $this->componentClasses[] = $name;
        }
        return $this;
    }

    /**
     * Returns additional component classnames.
     *
     * @return array
     */
    public function getComponentClasses()
    {
        return $this->componentClasses;
    }

    /**
     * Returns true when an
     * additional component class name exists.
     *
     * @param string
     * @return bool
     */
    public function hasComponentClass($name)
    {
        if (in_array($name, $this->componentClasses)) {
            return true;
        }
        return false;
    }

    /**
     * Returns html markup.
     *
     * @param bool $componentWrap – when false, markup will not get wrapped
     * @return string
     */
    public function render($componentWrap = true)
    {
        if (!$componentWrap) {
            return $this->markup();
        }

        $namespace = Config::matchNamespace(get_class($this));

        $wrap = Config::get($namespace, 'wrapComponent');
        $docking = Config::get($namespace, 'docking');

        if (isset($this->wrapTag)) {
            $wrap['tag'] = $this->wrapTag;
        }

        $markup = $this->markup();

        if (!$markup) {
            return '';
        }

        $m = [];

        if ($docking['wrap']) {
            $m[] = '<' . $docking['tag'] . ' data-' . $docking['class'] . '="1">';
        }

        if ($wrap['wrap']) {
            $m[] = '<' . $wrap['tag'] . ' class="'.$this->getComponentClass().'">';
        }

        $m[] = $markup;

        if ($wrap['wrap']) {
            $m[] = '</' . $wrap['tag'] . '>';
        }

        if ($docking['wrap']) {
            $m[] = '</' . $dockingTag . '>';
        }

        return implode('', $m);
    }

    /**
     * Returns component wrapper class name.
     *
     * @param none
     * @return nothing
     */
    protected function getComponentClass()
    {
        $classname = strtolower(get_class($this));

        $classname = explode('\\', $classname);

        $additionalClasses  = $this->getComponentClasses();

        $namespace = Config::matchNamespace(get_class($this));

        if ($wrapClass = Config::get($namespace, 'wrapComponent.class')) {
            // Custom wrap class is defined in namespaces config.
            $classname = $wrapClass . ' ' . $classname[ count($classname) - 1 ];

        } else {
            // No wrap class defined in cofig. Choose class name.
            $classname = trim(implode(' ', $classname));
        }

        if ($additionalClasses) {
            // Add additional classnames
            // – these may be set by setComponentClass()
            $classname .= ' ' . implode(' ', $additionalClasses);
        }

        $classname = implode(' ', array_unique(explode(' ', $classname)));

        if (!empty($this->removeComponentClasses)) {
            // Remove default component classes.
            $c = [];
            foreach (explode(' ', $classname) as $index => $cn) {
                if (!in_array($cn, $this->removeComponentClasses)) {
                    $c[] = $cn;
                }
            }
            $classname = implode(' ', $c);
        }

        return trim($classname);
    }

    /**
     * Returns component's data.
     *
     * @param none
     * @return array
     */
    public function toData()
    {
        $parser = new HtmlParser($this->markup());
        $parsed = $parser->toData();
        $data = [
          '__component' => $this->getType(),
          'content' => [],
        ];
        foreach ($parsed as $key => $value) {
            $data['content'][$key] = $value;
        }
        return $data;
    }

    /**
     * Shortcut: Returns property content by key:
     * - Strings and numbers
     * - or rendered components
     *
     * @param string $key
     * @return string
     */
    public function content($key)
    {
        return $this->getContentMarkupByKey($key);
    }

    /**
     * @see content()
     *
     * @param string $key
     * @return string
     * @deprecated
     */
    public function cr($key)
    {
        return $this->content($key);
    }

    /**
     * Shortcut: Returns property object by key.
     *
     * @param string $key
     * @return object
     */
    public function object($key)
    {
        return $this->getContentByKey($key);
    }

    /**
     * @see object()
     *
     * @param string $key
     * @return object
     * @deprecated
     */
    public function co($key)
    {
        return $this->object($key);
    }

    /**
     * Returns data property.
     *
     * @param string $key (optional)
     * @return array|mixed
     */
    public function getData($key = null)
    {
        if (!isset($this->data['data'])) {
            return null;
        }
        if ($key) {
            if (isset($this->data['data'][$key])) {
                return $this->data['data'][$key];
            }
            return null;
        }
        return $this->data['data'];
    }
}
