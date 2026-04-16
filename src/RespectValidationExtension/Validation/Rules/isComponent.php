<?php

namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class isComponent extends AbstractRule
{
    public function validate($input): bool
    {
        if (is_object($input) && is_subclass_of($input, 'wwaz\Components\BaseComponent')) {
            return true;
        }
        return false;
    }
}
