<?php

namespace wwaz\Components;

class FragmentFactory
{
    /**
     * Component namespace(s).
     *
     * @var array
     */
    protected static $namespaces = [
      __NAMESPACE__ . '\\Fragment',
    ];

    /**
     * Adds component namespace.
     *
     * @param string $namespace
     * @return self
     */
    public static function addNamespace($namespace)
    {
        self::$namespaces[] = $namespace;

        return self;
    }

    /**
     * Returns model.
     *
     * @param string $type
     * @param array $data
     * @return object
     */
    public static function make($type, $data)
    {
        if ($class = self::getModelClass($type, $data)) {
            return $class;
        }

        return false;
    }

    /**
     * Returns model.
     *
     * @param string $type
     * @param array $data
     * @return object
     */
    protected static function getModelClass($type, $data)
    {
        if (strpos($type, '.') === false) {
            return false;
        }

        $type = explode('.', $type);

        foreach (self::$namespaces as $index => $namespace) {

            $classname = [$namespace];

            for ($i = 0; $i < count($type); $i++) {
                $classname[] = ucfirst(strtolower($type[$i]));
            }

            $classname = implode('\\', $classname);

            if (!class_exists($classname)) {
                throw new \Exception('class ' . $classname . ' does not exist');
            }

            return new $classname($data);
        }
    }
}
