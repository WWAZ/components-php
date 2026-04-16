<?php

namespace wwaz\Components\Helper\Arrays;

class Flatten
{
    /**
     * Flattens array.
     *
     * e.g.
     * ['path' => 'to' => 'value']
     * --> ['path.to' => 'value']
     *
     * @param array $array
     * @return array
     */
    public static function flatten($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flatten($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $result;
    }

    /**
     * Deflattens array.
     *
     * e.g. ['path.to' => 'value']
     * --> ['path' => 'to' => 'value']
     *
     * @param array $array – flattened array
     * @return array
     */
    public static function deflatten($array)
    {
        return self::unflatten($array);
    }

    /**
     * Helper for deflatten():
     * Deflattens array path.
     *
     * @param array $data – e.g. ['path.to' => 'value']
     * @param array $exploded – exploded path – e.g. [path, to]
     * @param mixed $value – e.g. 'value'
     * @return array
     */
    protected static function deflattenPath($data, $exploded, $value)
    {
        $temp = &$data;
        foreach ($exploded as $key) {
            $temp = &$temp[$key];
        }
        $temp = $value;
        unset($temp);
        return $data;
    }


    protected static function unflatten($data)
    {
        $output = [];
        foreach ($data as $key => $value) {
            $parts = explode('.', $key);
            $nested = &$output;
            while (count($parts) > 1) {
                $nested = &$nested[array_shift($parts)];
                if (!is_array($nested)) {
                    $nested = [];
                }
            }
            $nested[array_shift($parts)] = $value;
        }
        return $output;
    }
}
