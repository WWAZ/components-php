<?php

namespace wwaz\Components\Render;

use wwaz\Components\Factory;
use wwaz\Components\Helper\Arrays\Flatten;

/**
 * Renders html
 * from given strapi component data
 */
class StrapiRenderer
{
    /**
     * Strapi api data
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Renders content.
     *
     * @param none
     * @return string html
     */
    public function render()
    {

        $data = $this->build($this->data);

        // if( defined('LOOK_AT') ){
        //   if( LOOK_AT ){
        //     if( isset($data[LOOK_AT]) ){
        //       print_r($data[LOOK_AT]);
        //       return $data[LOOK_AT]->render();
        //     } else {
        //       die('index ' . LOOK_AT . ' is undefined.');
        //     }
        //   }
        // }

        $m = '';
        for ($i = 0; $i < count($data); $i++) {
            if (is_object($data[$i])) {
                $m .= $data[$i]->render();
            } else {
                $m .= '<div class="unknown-component">'.$i . ') ' . $data[$i].'</div>';
            }
        }

        return $m;
    }

    /**
     * Build components recursively.
     *
     * @param array $data
     * @return string html
     */
    protected function build($data)
    {

        $result = [];

        for ($i = 0; $i < count($data); $i++) {

            if ($cn = Factory::keyIsComponentName($data[$i]['__component'])) {

                $data[$i] = $this->removeKey($data[$i], '__component');

                $result[] = Factory::make($cn, $data[$i]);

            } else {
                $result[] = 'unknown component: ' . $data[$i]['__component'];
            }
        }

        return $result;
    }

    /**
     * Removes key in recursive array.
     *
     * @param array $data
     * @param string $keyname
     * @return array
     */
    protected function removeKey($data, $keyname)
    {
        if (isset($data[0])) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i] = $this->removeKeyRecursive($data[$i], $keyname);
            }
            return $data;
        }
        return $this->removeKeyRecursive($data, $keyname);
    }

    /**
     * Helper for removeKey()
     *
     * @param array $data
     * @param string $keyname
     * @return array
     */
    protected function removeKeyRecursive($data, $keyname)
    {
        $data = Flatten::flatten($data);
        foreach ($data as $path => $value) {
            $path = explode('.', $path);
            $last = $path[count($path) - 1];
            if ($last === $keyname) {
                unset($data[implode('.', $path)]);
            }
        }
        return Flatten::deflatten($data);
    }

}
