<?php

namespace wwaz\Components;

use wwaz\Components\Helper\Strings\Json;
use wwaz\Components\Helper\Arrays\Flatten;
use wwaz\Components\Factory;

/**
 * Component Batch.
 *
 * Takes component batch array
 * and converts it into
 * a nested component object.
 */
class Batch
{
    protected $data;

    /**
     * Constructor.
     *
     * @param array|string – Batch as array or json
     */
    public function __construct($data)
    {
        $this->data = $this->validate($data);
    }

    /**
     * Returns batch as html.
     *
     * @param none
     * @return string
     */
    public function toHtml()
    {
        // Type of component
        $type = $this->data['type'];

        // data of component
        $data = $this->data;

        // Unset type
        unset($data['type']);

        $data = $this->buildComponentContainmentRecursive($data);

        // Create the whole component.
        $component = Factory::make($type, $data);

        // Return whole component's html
        return $component->toHtml();
    }

    public function toData()
    {
        // Type of component
        $type = $this->data['type'];

        // data of component
        $data = $this->data;

        // Unset type
        unset($data['type']);

        $data = $this->buildComponentContainmentRecursive($data);

        // Create the whole component.
        $component = Factory::make($type, $data);

        // Return whole component's html
        return $component->toData();
    }

    /**
     * Builds component objects from batch data recursively.
     *
     * @param array $data - batch
     * @return array
     */
    protected function buildComponentContainmentRecursive($data)
    {
        // Flatten array
        $arr = Flatten::flatten($data);

        // Get max depth
        $depth = $this->getMaxLevel($arr);

        // extract non contain attributes
        $xxx = $this->getNonContainsAttributes($arr);

        // Group flattened data by components
        $arr = $this->groupDataByComponents($arr);

        // Add component objects to data
        // starting at highest level
        for ($level = $depth; $level > 0; $level--) {
            $arr = $this->makeComponentOnLevel($level - 1, $arr);
        }

        // Put components from higher levels
        // into 'contain' of components in lower levels
        for ($level = $depth; $level > 0; $level--) {
            $arr = $this->addComponentOnLevel($level, $arr);
        }

        // Add non-component attributes
        // back to data
        foreach ($xxx as $key => $value) {
            $arr[$key] = $value;
        }

        // Deflatten array again
        $arr = Flatten::deflatten($arr);

        // Return deflattened array
        return $arr;
    }

    protected function addComponentOnLevel($level, $arr)
    {
        $items = $this->getItemsOnLevel($level, $arr);
        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            if ($item['addToKey']) {
                $arr[ $item['addToKey'] ]->addContent($item['value']);
                unset($arr[ $item['key'] ]);
            }
        }
        return $arr;
    }

    protected function getItemsOnLevel($level, $arr)
    {
        $res = [];
        foreach ($arr as $key => $value) {
            if ($this->getKeyLevel($key) === $level) {
                $res[] = [
                  'key' => $key,
                  'addToKey' => $this->getBaseParentBasePath($key),
                  'value' => $value
                ];
            }
        }
        return $res;
    }

    protected function makeComponentOnLevel($level, $arr)
    {
        foreach ($arr as $key => $value) {
            if ($this->getKeyLevel($key) === $level) {

                if (isset($value['type'])) {

                    $data = $value;
                    unset($data['type']);

                    if (is_array($data)) {
                        if (isset($data[0])) {
                            if (is_string($data[0])) {
                                $data = $data[0];
                            }
                        }
                    }

                    $arr[$key] = Factory::make($value['type'], $data);
                }
            }
        }
        return $arr;
    }

    protected function getNonContainsAttributes($arr)
    {
        $res = [];
        foreach ($arr as $key => $value) {
            if (strpos($key, 'content') === false) {
                $res[$key] = $value;
            }
        }
        return $res;
    }

    /**
     * Groups flattened data by components
     * by assigning component's data
     * to their basepath keys.
     *
     * e.g.
     *
     * Given array:
     * [
     * 'contains.0.type' => Tag.A
     * 'contains.0.attributes.href' => url.html
     * 'contains.0.attributes.target' =>
     * 'contains.0.contains.0' => Click me!
     * 'contains.1' => c2
     * ]
     *
     * Resulting array:
     * [
     *  'contains.0' => ['type' => 'Tag.A', 'attributes' => ['href' => 'url.html', 'target' => ]]
     *  'contains.0.contains.0' => 'Click me!'
     *  'contains.1' => 'c2'
     * ]
     *
     *
     * @param array $arr
     * @return array
     */
    protected function groupDataByComponents($arr)
    {
        $res = [];
        foreach ($arr as $key => $value) {
            if (strpos($key, 'content') !== false) {
                $basepath = $this->getBasePath($key);
                $k = str_replace($basepath, '', $key);
                $k = substr($k, 0, 1) === '.' ? substr($k, 1, strlen($k)) : $k;
                $k = $k == '' ? 'content' : $k;
                if ($k !== 'content') {
                    // Component's attributes
                    $res[$basepath][$k] = $value;
                    // Convert path (e.g. attributes.href) to array ['attributes' => ['href' => url.html] ...]
                    $res[$basepath] = Flatten::deflatten($res[$basepath]);
                } else {
                    // Component's 'content' values
                    $res[$basepath] = $value;
                }
            }
        }
        return $res;
    }

    protected function getBaseParentBasePath($key)
    {
        // Get parent level
        $e = explode('content', $this->getBasePath($key));
        $parentLevel = count($e) - 1;

        // Get parent base path
        $e = explode('.', $key);
        $path = [];
        $cntContains = 0;
        for ($i = 0; $i < count($e); $i++) {
            if ($e[$i] === 'content') {
                $cntContains++;
            }
            if ($cntContains < $parentLevel && ($e[$i] === 'content' || is_numeric($e[$i]))) {
                $path[] = $e[$i];
            }
        }
        return implode('.', $path);

    }

    protected function getBasePath($key)
    {
        $e = explode('.', $key);
        $path = [];
        for ($i = 0; $i < count($e); $i++) {
            if ($e[$i] === 'content' || is_numeric($e[$i])) {
                $path[] = $e[$i];
            }
        }
        return implode('.', $path);
    }

    protected function getLastKey($key)
    {
        $e = explode('.', $key);
        $last = array_pop($e);
        if (is_numeric($last)) {
            // we're only interests in 'type' or 'content' – not contains.0 ...
            $last = array_pop($e);
        }
        return $last;
    }

    protected function getKeyLevel($key)
    {
        $level = 0;
        $e = explode('.', $key);
        foreach ($e as $name) {
            if ($name === 'content') {
                $level++;
            }
        }
        return $level;
    }

    protected function getMaxLevel($flattened)
    {
        $maxDepth = 0;
        foreach ($flattened as $key => $value) {
            $depth = $this->getKeyLevel($key);
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
        return $maxDepth;
    }

    protected function validate($data)
    {
        if (Json::isJson($data)) {
            $data = Json::toArray($data);
        }
        return $data;
    }
}