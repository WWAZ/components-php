<?php

namespace wwaz\Components\Factory;

use wwaz\Components\Factory;

class RecursiveComponentBuilder
{
    /**
     * Builds component array.
     *
     * @param array $data
     * @param array $result (internal only)
     * @return array
     */
    public static function build($data, $result = [])
    {
        if (empty($data)) {
            return [];
        }

        foreach ($data as $k => $v) {

            $cn = Factory::keyIsComponentName($k);

            if (is_array($v) && $cn) {
                // components

                if (isset($v[0])) {
                    // indexed array containing multiple components.
                    foreach ($v as $index => $comp) {
                        $result[$k][] = Factory::make($cn, self::build($comp, []));
                    }

                } else {
                    // associative array containing one component.
                    $result[$k] = Factory::make($cn, self::build($v, []));
                }

            } elseif (is_array($v) && !$cn) {
                // non-components

                if (isset($v[0])) {
                    $result[$k] = self::build($v, []);

                } else {
                    $result[$k] = self::build($v, []);
                }

            } else {
                $result[$k] = $v;

            }
        }

        return $result;
    }

}
