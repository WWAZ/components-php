<?php

namespace wwaz\Components;

use Gajus\Dindent\Indenter;
use wwaz\Components\Validate\DataValidator;
use wwaz\Components\Helper\Arrays\Flatten;
use wwaz\Components\Helper\Arrays\Merge;
use wwaz\Components\Parse\HtmlParser;

abstract class BaseComponent
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
      'attributes' => []
    ];

    protected $reservedPropertyKeys = [
      'attributes',
      'content'
    ];

    /**
     * Data.
     *
     * @var array
     */
    protected $data = [
      'content' => null
    ];

    /**
     * Constructor.
     *
     * @param array $data
     * @param mixed $contains
     */
    public function __construct($data)
    {
        $this->mergeProperties();
        if (is_string($data) || $this->isComponentObject($data)) {
            $data = [
              'content' => [$data]
            ];
        }
        $this->data = $this->validateData($data);
    }

    /**
     * Merges property 'properties' of all objects
     * in inheritance chain recursively.
     * Inspired by @see https://stackoverflow.com/questions/14417391/how-to-inherit-parent-class-array-properties-by-merging-array/36785774
     *
     * @param none
     * @return nothing
     */
    protected function mergeProperties()
    {

        $props = [$this->properties];

        $class = get_called_class();
        while ($class = get_parent_class($class)) {
            $props[] = get_class_vars($class)['properties'];
        }

        $res = [];
        for ($i = count($props) - 1; $i >= 0; $i--) {
            $res = Merge::merge($res, $props[$i]);
        }

        $this->properties = $res;
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
        if (!isset($this->data['attributes'])) {
            $this->data['attributes'] = [];
        }
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
        if (!isset($this->data['attributes'])) {
            return null;
        }
        return isset($this->data['attributes'][$name]) ? $this->data['attributes'][$name] : null;
    }


    /**
     * Returns all attributes.
     *
     * @param string name
     * @param array|null
     */
    public function getAttributes()
    {
        if (isset($this->data['attributes'])) {
            if (is_array($this->data['attributes']) && !empty($this->data['attributes'])) {
                return $this->data['attributes'];
            }
        }
        return null;
    }


    /**
     * Returns attributes as html markup.
     *
     * @param none
     * @return string
     */
    protected function htmlAttributes()
    {

        $m = [];

        if ($attributes = $this->getAttributes()) {
            foreach ($attributes as $k => $v) {

                // if( $k === 'id' || $k === 'class' || strpos('data-', $k) !== false ){

                if (!is_array($v)) {
                    if (!is_null($v)) {
                        $m[] = $k . '="' . trim($v) . '"';
                    }
                } else {
                    // e.g. case class names [cn1, cn2 ...]
                    $m[] = $k . '="' . implode(' ', $v) . '"';
                }

                // }
            }
            if (!empty($m)) {
                return ' ' . implode(' ', $m);
            }
        }

        return false;
    }

    /**
     * Adds new class name before existing class names.
     *
     * @param string $name
     * @return self
     */
    public function prependClass($name)
    {
        $class = $this->getAttribute('class');
        if (!$this->hasClass($name)) {
            if (is_array($class)) {
                array_unshift($class, $name);
            } else {
                $class = is_string($class) ? trim($name . ' ' . $class) : $name;
            }
        }
        $this->setAttribute('class', $class);
        return $this;
    }

    /**
     * Adds class name.
     *
     * @param string name
     * @param self
     */
    public function addClass($name)
    {
        $class = $this->getAttribute('class');
        if (!$class) {
            $class = [];
            $this->setAttribute('class', $class);
        }
        if (!$this->hasClass($name)) {
            if (is_array($class)) {
                $class[] = $name;
            } else {
                $class = is_string($class) ? trim($class . ' ' . $name) : $name;
            }
        }
        $this->setAttribute('class', $class);
        return $this;
    }

    /**
     * Returns true when given classname exists in class attribute.
     *
     * @param string name
     * @param bool
     */
    public function hasClass($name)
    {
        $class = $this->getAttribute('class');
        if (is_null($class)) {
            return false;
        }
        if (is_array($class)) {
            if (in_array($name, $class)) {
                return true;
            }
        } else {
            $e = explode(' ', $class);
            if (in_array($name, $e)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns class list.
     *
     * @return array
     */
    public function classList()
    {
        $class = $this->getAttribute('class');
        if (is_array($class)) {
            return $class;
        } else {
            $e = explode(' ', $class);
            return $class;
        }
        return false;
    }

    /**
     * Returns class attribute html.
     *
     * class="..."
     *
     * @param bool $addWhitespace
     * @return string
     */
    public function getClassAttributeHtml($addWhitespace = true)
    {
        $m = '';
        if ($addWhitespace) {
            $m .= ' ';
        }
        $m .= 'class="'.implode(' ', $this->classList()).'"';
        return $m;
    }

    /**
     * Returns merged properties of
     * instance and all parent objects.
     *
     * @param none
     * @return array
     */
    public function getProperties()
    {
        $properties = $this->properties;
        $parents = class_parents($this);
        foreach ($parents as $classname) {
            $classVars = get_class_vars($classname);
            if (isset($classVars['properties'])) {
                $properties = array_merge($classVars['properties'], $properties);
            }
        }
        return $properties;
    }


    /**
     * Validates inpout data.
     *
     * @param array $data
     * @return nothing
     */
    protected function validateData($data)
    {

        $this->transformProperties();

        $data = $this->propertyDataStructureCorrection($data);

        $Validator = new DataValidator($this->getProperties(), $data);
        $result = $Validator->validate();
        if (!empty($result['errors'])) {
            $this->errorWarning($result['errors']);
            return false;
        }
        return $result['data'];
    }

    /**
     * Transforms properties
     *
     * @param none
     * @return nothing
     */
    protected function transformProperties()
    {

        $properties = $this->properties;

        $transform = ['content', 'data'];

        foreach ($transform as $property) {

            if (isset($properties[$property])) {

                // Content transfomation:
                // To make 'content' property more convenient,
                // it should by possible to defined contents
                // in a destructred way.
                //
                // Regular expected content array looks like this:
                // [
                //  'content#1' => '*',
                //  'content#2' => '*',
                //  'content#3' => 'isComponent'
                // ]
                //
                // Input may look like this:
                // [
                //  'content#1',                  <-- destructured without rule
                //  'content#2',                  <-- destructured without rule
                //  'content#3' => 'isComponent'  <-- regular with rule
                // ]
                //
                // Result will look like this
                // [
                //  'content#1' => '*',
                //  'content#2' => '*',
                //  'content#3' => 'isComponent'
                // ]
                //
                // Here we're transforming destructured array types
                // to regular array types.
                if (is_array($properties[$property]) && isset($properties[$property][0])) {
                    for ($i = 0; $i < count($properties[$property]); $i++) {

                        if (isset($properties[$property][$i])) {
                            // Destructured value without rule
                            $value = $properties[$property][$i];
                            $properties[$property][$value] = '*';
                            unset($properties[$property][$i]);
                        }

                    }
                }
                $this->properties = $properties;
            }
        }

    }

    /**
     * Property data structure correction.
     *
     * It should by possible to pass attribute properties
     * as root key – without adding them in an e.g. attribute array.
     *
     * e.g. given is a new fragment.html.a component:
     * [
     *  'href' => 'myurl.html',     <-- is attribute
     *  'target' => '_blank'        <-- is attribute
     *  'content' => 'click me!'
     * ]
     *
     * This method will detect e.g. 'attributes' and pushes them
     * into the attribute array.
     *
     * Result:
     * [
     *  'content' => 'click me!',
     *  'attributes' => [
     *    'href' => 'myurl.html',
     *    'target' => '_blank'
     *   ]
     * ]
     *
     * @param array $data
     * @return array $data
     */
    protected function propertyDataStructureCorrection($data)
    {

        // Which property keys contain an array?
        $arrayProperties = [];
        $props = $this->getProperties();
        if (is_array($props)) {
            foreach ($props as $key => $prop) {
                if (is_array($prop)) {
                    $arrayProperties[] = $key;
                }
            }
        }

        $added = [];

        foreach ($arrayProperties as $AP) {

            if (isset($props[$AP]) && is_array($props[$AP])) {

                // Set $AP (e.g. = 'attributes') property
                // in data when not existing.
                if (!isset($data[$AP])) {
                    $data[$AP] = [];
                }

                // Add attribute properties and unset the root key.

                foreach ($props[$AP] as $key => $val) {
                    if (isset($data[$key])) {
                        if (!isset($data[$AP][$key])) {
                            $data[$AP][$key] = $data[$key];
                            unset($data[$key]);
                            $added[] = $key;
                        }
                    }
                }



            }
        }

        // Some data keys may be left,
        // those which are not defined in properties
        // and are optional (e.g. data-x ...).
        // In this case we add every left over
        // as 'attribute'.
        // This does not apply to keys
        // that are defined as root attribute.
        // foreach($data as $key => $value){
        //   if( !in_array($key, $added) ){
        //     // Not added to attributes yet
        //     if( !in_array($key, $this->reservedPropertyKeys) ){
        //       // Is not a preserved key.
        //       if( !array_key_exists($key, $props) ){
        //         // Not defined as root attribute.
        //         $data['attributes'][$key] = $data[$key];
        //         unset($data[$key]);
        //       }
        //     }
        //   }
        // }


        return $data;
    }

    protected function errorWarning($errors)
    {
        $m = [];
        $m[] = '<h2>Warning</h2>';
        foreach ($errors as $index => $error) {
            $efk = array_key_first($error);
            $m[] = $this->getType() . ': ' . ($index + 1) . ') ' . $efk . ': ' . $error[$efk];
        }
        die(implode("<br>\n", $m) . "<br>\n<br>\n");
    }


    /**
     * Returns instance type.
     *
     * @param none
     * @return string
     */
    protected function getType()
    {
        $classname = get_class($this);
        $classname = explode('\\', $classname);
        return trim(implode('.', $classname));
        // $record = 0;
        // $res = [];
        // foreach($classname as $cn){
        //   if( $record === 1 ){
        //     $res[] = $cn;
        //   }
        //   if( $cn === 'Model' || $cn === 'Components' ){
        //     $record = 1;
        //   }
        // }
        // return implode('.', $res);
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
    protected function isComponentObject($var)
    {
        if (is_object($var) && is_subclass_of($var, __NAMESPACE__ . '\BaseComponent')) {
            return true;
        }
        return false;
    }


    /**
     * Returns content elements.
     *
     * @param none
     * @return array
     */
    public function getContent()
    {
        return $this->data['content'];
    }


    /**
     * Returns content element's markup
     * of given key (index).
     *
     * @param integer|string $key
     * @return string
     */
    protected function getContentMarkupByKey($key)
    {
        if ($content = $this->getContentByKey($key)) {
            if ($this->isComponentObject($content)) {
                return $content->render();
            }
            return $content;
        }
    }


    /**
     * Returns content element
     * of given key (index).
     *
     * @param integer|string $key
     * @return string
     */
    protected function getContentByKey($key)
    {
        if (!isset($this->data['content'])) {
            return null;
        }
        if (!isset($this->data['content'][$key])) {
            return null;
        }
        return $this->data['content'][$key];
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

        if (!$content) {
            return '';
        }

        $m = '';

        if (is_array($content)) {

            for ($i = 0; $i < count($content); $i++) {
                $m .= $this->getContentMarkup($content[$i]);
            }

        } else {

            if ($this->isComponentObject($content)) {
                // Another component
                $m .= $content->render();
            } else {
                $m .= $content;
            }
        }
        return $m;
    }


    /**
     * Returns component's markup.
     *
     * @param none
     * @return string
     * @abstract
     */
    abstract protected function markup();


    /**
     * Returns html markup.
     *
     * @param none
     * @return string
     */
    public function render()
    {
        $indenter = new Indenter();
        return $indenter->indent($this->markup());
    }


    /**
     * Returns component's data.
     *
     * @param none
     * @return array
     */
    public function toData()
    {

        // echo 'TYPE????' . $this->getType() . "\n";
        // if( isset($this->data['type']) ){
        //   echo 'TYPE: ' . $this->data['type'] . "\n";
        // } else {
        //   echo 'NO TYPE!' . "\n";
        // }

        $data = Flatten::flatten($this->data);
        $result = ['type' => $this->getType()];

        foreach ($data as $key => $value) {

            if ($this->isComponentObject($value)) {
                $result[$key] = $value->toData();

            } else {
                if ($key === 'content') {
                    if (is_string($value)) {
                        $value = [$value];
                    }
                }
                $result[$key] = $value;
            }
        }

        return Flatten::deflatten($result);
    }
    // public function toData(){
    //   $parser = new HtmlParser($this->markup());
    //   $parsed = $parser->toData();
    //   // $data = [
    //   //   'composite' => $this->getType()
    //   // ];
    //   foreach($parsed as $key => $value){
    //     $data[$key] = $value;
    //   }
    //   return $data;
    // }

    /**
     * Adds content element.
     *
     * @param string|number|object
     * @return self
     */
    public function addContent($new)
    {
        if (is_array($new)) {
            for ($i = 0; $i < count($new); $i++) {
                $this->data['content'][] = $new[$i];
            }
        } else {
            $this->data['content'][] = $new;
        }
        return $this;
    }


}
