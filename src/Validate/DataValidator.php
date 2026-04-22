<?php

namespace wwaz\Components\Validate;

use wwaz\Components\Helper\Arrays\Flatten;
use wwaz\Components\Helper\Strings\StringConverter;
use wwaz\RespectValidationExtension\Validate as Validator;

class DataValidator
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties;

    /**
     * Data.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param array $properties
     * @param array $data
     */
    public function __construct($properties, $data)
    {
        $this->properties = $properties;
        $this->data = $data;
    }

    /**
     * Validates given data against given propeties.
     *
     * @param none
     * @return array [errors, propeties, data]
     */
    public function validate()
    {
        return $this->validateRecursive($this->properties, $this->data);
    }

    /**
     * Validates given data against given properties
     * recursively.
     *
     * @param array $properties
     * @param array $data
     * @return array [errors, properties, data]
     */
    protected function validateRecursive($properties, $data)
    {


        if (isset($data['content'])) {
            // Content is a collection object.
            // So, all values must be wrapped
            // in an array. To provide a simpler api,
            // we're allowing single input values,
            // and are wrapping them here.
            // E.g. 'content' => string|number|object
            // --> 'content' => [string|number|object]
            if (!is_array($data['content'])) {
                $data['content'] = [$data['content']];
            }
        }

        foreach ($properties as $key => $validationRules) {
            if (!isset($data[$key])) {
                if (strpos($key, 'array') !== false || strpos($key, 'arrayType') !== false) {
                    $data[$key] = [];
                } else {
                    $data[$key] = null;
                }
            }
        }

        $properties = Flatten::flatten($properties);
        $data = Flatten::flatten($data);

        foreach ($properties as $key => $validationRules) {
            $validationRules = explode('|', $validationRules);
            $properties[$key] = $validationRules;
        }

        $errors = [];

        $data = $this->addDefault($properties, $data);

        $errors = $this->validateRequired($errors, $properties, $data);

        $result = $this->validateValues($properties, $data);

        if (is_array($result)) {
            // Errors
            foreach ($result as $error) {
                $errors[] = $error;
            }
        }

        $data = Flatten::deflatten($data);

        return [
          'errors' => $errors,
          'properties' => $properties,
          'data' => $data,
        ];

    }

    /**
     * Validates value against rules, defined in properties.
     *
     * @param array $data – flattend array
     * @return true|array – true on success, array of error messages on error
     */
    protected function validateValues($properties, $data)
    {
        $errors = [];
        foreach ($properties as $key => $rules) {
            if (isset($data[$key])) {
                $validated = Validator::multiple($rules, $key, $data[$key]);
                if ($validated !== true) {
                    $errors[] = $validated;
                }
            }
        }
        if (!empty($errors)) {
            return $errors;
        }
        return true;
    }

    /**
     * Validates required values.
     *
     * @param array $errors – list of already found errors.
     * @param array $properties
     * @param array $data
     * @return array – empty on success, error messages on error.
     */
    protected function validateRequired($errors, $properties, $data)
    {
        foreach ($properties as $key => $rule) {
            if (in_array('required', $rule)) {
                if (!isset($data[$key])) {
                    $errors[] = [$key => '- is required'];
                }
            }
        }
        return $errors;
    }

    /**
     * Adds default value
     * when given in props and value is undefined.
     *
     * @param array $properties
     * @param array $data
     * @return array – $data
     */
    protected function addDefault($properties, $data)
    {
        foreach ($properties as $key => $rule) {
            if ($value = $this->in_array_contains('default', $rule)) {
                if (!isset($data[$key])) {
                    $value = explode(':', $value);
                    if (isset($value[1])) {
                        $data[$key] = StringConverter::convert($value[1]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Find part of string in array
     * and returns it's value.
     *
     * e.g.
     * needle: 'default'
     * array: ['default:MyDefaultValue', 'someOtherKey:someOtherValue']
     * --> return 'default:MyDefaultValue'
     *
     * @param string $needle
     * @param array $array – indexed array
     * @return array – $data
     */
    protected function in_array_contains($needle, $array)
    {
        foreach ($array as $key => $value) {
            if (strpos($value, $needle) !== false) {
                return $value;
            }
        }
        return false;
    }




}
