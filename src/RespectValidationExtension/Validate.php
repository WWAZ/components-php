<?php

namespace wwaz\RespectValidationExtension;

use Respect\Validation\Factory;

$FactoryInstance = (new Factory())
    ->withRuleNamespace('wwaz\\RespectValidationExtension\\Validation\Rules')
    ->withExceptionNamespace('wwaz\RespectValidationExtension\\Validation\\Exceptions');

Factory::setDefaultInstance(
    $FactoryInstance
);

use Respect\Validation\Rules;

class Validate
{
    public static function multiple($rules, $key, $value)
    {
        $errors = [];

        $ruleClasses = self::getRuleClasses($rules);

        if (! empty($ruleClasses)) {
            $ruleSet = new Rules\AllOf(...$ruleClasses);
            try {
                $ruleSet->assert($value);
            } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
                // Error found. Collect it.
                $errors[$key] = $e->getFullMessage();
            }
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    protected static function getRuleClasses($rules)
    {
        $ruleClasses = [];

        for ($i = 0; $i < count($rules); $i++) {

            $rule = $rules[$i];

            [$rule, $arguments] = self::splitRulenameAndArguments($rule);

            if (class_exists('\\Respect\\Validation\\Rules\\' . $rule)) {
                // echo'add rule (n)' . $rule . "\n";
                $cn            = '\\Respect\\Validation\\Rules\\' . $rule;
                $ruleClasses[] = new $cn(...$arguments);

            } elseif (class_exists('wwaz\\RespectValidationExtension\\Validation\\Rules\\' . $rule)) {
                // echo'add rule (e)' . $rule . "\n";
                $cn            = 'wwaz\\RespectValidationExtension\\Validation\\Rules\\' . $rule;
                $ruleClasses[] = new $cn(...$arguments);
            } else {
                // echo 'Ø: ' . $rule . "\n";
            }
        }

        return $ruleClasses;
    }

    protected static function splitRulenameAndArguments($rule)
    {
        $arguments = [];
        if (strpos($rule, ':') !== false) {
            $e    = explode(':', $rule);
            $rule = $e[0];
            $v    = $e[1];
            if (isset($v)) {
                if (strpos($v, ',')) {
                    $arguments = explode(',', $v);
                } else {
                    $arguments = [$v];
                }
            }
        }

        return [
            $rule,
            $arguments,
        ];
    }
}
