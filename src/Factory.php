<?php

namespace wwaz\Components;

use wwaz\Components\Factory\RecursiveComponentBuilder;

class Factory
{
    /**
     * Cache for non-component types.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Cache for component namespaces.
     *
     * @var array
     */
    protected static $componentCache = [];

    /**
     * Component namespace(s).
     *
     * @var array
     */
    protected static $namespaces = [
      __NAMESPACE__,
    ];

    /**
     * When true, errors will be thrown.
     * e.g. when component does not exist.
     *
     * @var bool
     */
    protected static $throwErrors = false;

    /**
     * Adds component namespace.
     *
     * @param string $namespace
     */
    public static function addNamespace(string $namespace)
    {
        array_unshift(self::$namespaces, $namespace);
    }

    /**
     * Sets throw errors.
     *
     * @param bool $bool
     */
    public static function setThrowErrors(bool $bool)
    {
        self::$throwErrors = $bool;
    }

    /**
     * Returns model.
     *
     * @param string $type
     * @param array|object $data
     * @return object
     */
    public static function make(string $type, $data)
    {
        // Try get cached class.
        $classname = self::componentCacheGet($type);

        if (!$classname) {
            // Try to search class by type name.
            if ($classname = self::getModelClassName($type)) {
                self::componentCacheSet($type, $classname);
            }
        }

        if ($classname) {
            // Build component recursively.
            return new $classname(RecursiveComponentBuilder::build($data));
        }

        if (self::$throwErrors || 1 === 1) {
            throw new \Exception('class ' . $classname . ' does not exist in namespaces: [' . implode(', ', self::$namespaces) . ']');
        }

        return false;
    }

    /**
     * Returns true when component class exists.
     *
     * @param string $type
     * @return bool
     */
    public static function exists(string $type)
    {
        if ($cached = self::componentCacheGet($type)) {
            return $cached;
        }
        if ($classname = self::getModelClassName($type)) {
            if (self::isDeclared($classname)) {
                return $classname;
            }
        }

        return false;
    }

    /**
     * Returns true when given class is declared.
     *
     * @param string $classname – namespace
     * @return bool
     */
    protected static function isDeclared($classname)
    {
        if (in_array($classname, get_declared_classes())) {
            return true;
        }

        return false;
    }

    /**
     * Searches model.
     *
     * @param string $type
     * @param array $data
     * @return object
     * @throws \Exception – when model not found.
     */
    protected static function getModelClassName($type)
    {
        // 1. try to get model class
        // directly when passed classname passed by type.
        // e.g. 'wwaz.Component.Callaction'
        // --> wwaz\Component\Callaction
        if (strpos($type, '.') !== false) {
            $e = explode('.', $type);
            $e = implode('\\', $e);
            if (class_exists($e)) {
                return $e;
            }
        }

        // 2. Search model in all known variants
        // and registered namespaces.
        return self::searchVariants($type);
    }

    /**
     * Searches model in all knwon variants
     * and registered namespaces.
     *
     * @param string $type
     * @return string|bool
     */
    protected static function searchVariants($type)
    {
        $variants = self::createPossibleVariants($type);

        for ($i = 0; $i < count($variants); $i++) {
            if (class_exists($variants[$i])) {
                // It's tremendously important to cache the class here.
                // ----------------------------------------------------
                // Why? class_exists() triggers spl_autoload()
                // and declares the class. Hitting class_exists twice
                // would lead to a Fatal error: "Cannot declare class [namespace],
                // because the name is already in use."
                // @see https://github.com/rectorphp/rector/issues/5024
                // Caching prevents this error.
                self::componentCacheSet($type, $variants[$i]);
                return $variants[$i];
            }
        }

        return false;
    }

    /**
     * Returns all possible namespaces
     * of given component type name.
     *
     * @param string $type
     * @return array
     */
    protected static function createPossibleVariants($type)
    {
        $containsDirectorySeperator = false;

        // Contains directory seperator like . or \ ?
        if (strpos($type, '.') !== false) {
            $type = explode('.', $type);
            $containsDirectorySeperator = true;

        } elseif (strpos($type, '\\') !== false) {
            $type = explode('\\', $type);
            $containsDirectorySeperator = true;
        }

        if (is_array($type)) {
            // e.g. dir.name || dir\name
            // --> Dir\Name
            for ($i = 0; $i < count($type); $i++) {
                if (!ctype_upper(substr($type[$i], 0, 1))) {
                    $type[$i] = ucfirst(strtolower($type[$i]));
                } else {
                    $type[$i] = $type[$i];
                }
            }
            $type = implode('\\', $type);

        } else {
            $type = ucfirst($type);
        }

        if (self::isSnakeCase($type)) {
            $type = self::camelCase($type);
        }

        $variants = [];

        foreach (self::$namespaces as $index => $namespace) {
            // name
            $variants[] = implode('\\', [$namespace, $type]);
            if (!$containsDirectorySeperator) {
                // name/name
                $variants[] = implode('\\', [$namespace, $type, $type]);
            }
            // name/Component
            $variants[] = implode('\\', [$namespace, $type, 'Component']);
            // name/Index
            $variants[] = implode('\\', [$namespace, $type, 'Index']);
        }

        return $variants;
    }

    /**
     * Returns true when given string is in snakeCase.
     *
     * @param string $str
     * @return bool
     */
    protected static function isSnakeCase($str)
    {
        if (strpos($str, '_') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Converts given string to camel case.
     *
     * @param string $str
     * @return string
     */
    protected static function camelCase($str)
    {
        $str = explode('_', $str);
        for ($i = 0; $i < count($str); $i++) {
            $str[$i] = ucfirst($str[$i]);
        }

        return ucfirst(implode('', $str));
    }

    /**
     * Returns key name
     * when data key is known as a component.
     *
     * @param string $key
     * @return string
     */
    public static function keyIsComponentName($key)
    {
        if (is_numeric($key)) {
            return false;
        }

        $cache = self::cacheGet($key);

        if (!is_null($cache)) {
            return $cache;
        }


        $okey = $key;

        if (strpos($key, '.') !== false) {
            $key = explode('.', $key);
            $key = $key[count($key) - 1];
        }

        if (strpos($key, '-') !== false) {
            $e = explode('-', $key);
            $key = '';
            for ($i = 0; $i < count($e); $i++) {
                $key .= ucfirst(strtolower($e[$i]));
            }
        }

        // Check direct
        if ($cn = self::exists($key)) {
            self::cacheSet($okey, $key);
            return $key;
        }

        // Check folder: [Component]/[Conponent]
        $test = $key . '\\' . $key;
        if ($cn = self::exists($test)) {
            self::cacheSet($okey, $test);
            return $test;
        }

        self::cacheSet($okey, false);

        return false;
    }

    /**
     * Sets cache.
     *
     * @param string $key
     * @param mixed $value
     * @return nothing
     */
    protected static function cacheSet($key, $value)
    {
        self::$cache[$key] = $value;
    }

    /**
     * Returns cache item.
     *
     * @param string $key
     * @return mixed|null
     */
    protected static function cacheGet($key)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        return null;
    }

    /**
     * Sets cache.
     *
     * @param string $key
     * @param mixed $value
     * @return nothing
     */
    protected static function componentCacheSet($key, $value)
    {
        $key = strtolower($key);
        self::$componentCache[$key] = $value;
    }

    /**
     * Returns cache item.
     *
     * @param string $key
     * @return mixed|null
     */
    protected static function componentCacheGet($key)
    {
        $key = strtolower($key);
        if (isset(self::$componentCache[$key])) {
            return self::$componentCache[$key];
        }

        return null;
    }
}
