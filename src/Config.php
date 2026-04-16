<?php

namespace wwaz\Components;

use wwaz\Components\Helper\Arrays\Flatten;
use wwaz\Components\Helper\Arrays\Merge;

class Config
{
    /**
     * Config data.
     *
     * @var array
     */
    protected static $config = [];

    /**
     * Sets config data for given namespace.
     *
     * - Always merges global namespace config into new namespaces
     *    to make sure, that global settings are always present.
     * - Merges config of non-global namespaces when they already exist.
     *
     * @param string $namespace
     * @param array $config
     */
    public static function set($namespace, $config)
    {
        if (!is_array($config)) {
            return false;
        }

        if ($namespace !== 'global' && isset(self::$config[$namespace])) {
            // Existing namespace: merge new config with exitsting.
            self::$config[$namespace] = Merge::merge(self::$config[$namespace], $config);

        } elseif ($namespace === 'global' && isset(self::$config[$namespace])) {
            // Updating global namespace:
            // Merge new config data with existing config data.
            self::$config[$namespace] = Merge::merge(self::$config['global'], $config);

            // Merge new config into all other namespaces.
            foreach (self::$config as $ns => $val) {
                if ($ns !== 'global') {
                    self::$config[$ns] = Merge::merge(self::$config['global'], self::$config[$ns]);
                }
            }

        } else {
            // New namespace
            if (isset(self::$config['global'])) {
                // Global namespace exists – merge new with global.
                // to make sure, that global settings exist in new namespace aswell
                self::$config[$namespace] = Merge::merge(self::$config['global'], $config);
            } else {
                // Setting first namespace.
                self::$config[$namespace] = $config;
            }
        }
    }

    /**
     * Returns config data by
     * given namespace and dot seperated key.
     *
     * @param string $namespace
     * @param string $key
     */
    public static function get($namespace, $key = null)
    {
        if (!isset(self::$config[$namespace])) {
            return null;
        }
        if ($key) {
            return self::getByKey($namespace, $key);
        }
        return self::$config[$namespace];
    }

    public static function all()
    {
        return self::$config;
    }

    /**
     * Returns best matching config namespace.
     *
     * Example #1
     * Given is:      company\GreatComponents\Callaction
     * Registerd are: global,  company\GreatComponents, company\SomeMoreGratComponents
     * Result -->     company\GreatComponents
     *
     * Example #2
     * Given is:      company\UnknownComponents\Callaction
     * Registerd are: global,  company\GreatComponents, company\SomeMoreGratComponents
     * Result -->     global
     *
     * @param string $namespace
     * @return string $namespace
     */
    public static function matchNamespace($namespace)
    {
        if (isset(self::$config[$namespace])) {
            // Perfect match.
            return $namespace;
        }

        $res = [];

        // Find all namespaces, which names are beginning
        // with the namespace we're searching for.
        foreach (self::$config as $ns => $data) {
            if (strpos($namespace, $ns) === 0) {
                $res[] = $ns;
            }
        }

        // We've one matching namespace.
        // Return it.
        if (count($res) === 1) {
            return $res[0];
        }

        // We've found more matching namespaces.
        // Return longest.
        if (count($res) > 1) {
            $longest = 0;
            $index = null;
            for ($i = 0; $i < count($res); $i++) {
                $strlen = strlen($res[$i]);
                if ($strlen > $longest) {
                    $longest = $strlen;
                    $index = $i;
                }
            }
            return $res[$index];
        }

        // We've found no matching namespaces.
        // Return global config namespace.
        return 'global';
    }

    /**
     * Returns value by key.
     *
     * @param string $namespace
     * @param string $key
     */
    protected static function getByKey($namespace, $key)
    {
        $config = Flatten::flatten(self::$config[$namespace]);

        $res = [];

        foreach ($config as $k => $val) {

            if (strpos($k, $key) === 0) {

                if (strpos($k, '.') !== false) {
                    // Array; Nested value

                    // Replace inbetween
                    $k = str_replace($key . '.', '', $k);
                    // Replace end
                    $k = str_replace($key, '', $k);

                    if ($k !== '') {
                        // Key value pair
                        $res[$k] = $val;

                    } else {
                        // Just value
                        $res = $val;
                    }

                } else {
                    // Single, non-nested value
                    if ($k === $key) {
                        $res = $val;
                    }
                }
            }
        }

        if ($res) {
            if (is_array($res)) {
                return Flatten::deflatten($res);
            }
            return $res;
        }

        return null;
    }
}