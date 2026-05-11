<?php

namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class isString extends AbstractRule
{
    public function validate($input): bool
    {
        if (is_string($input)) {
            return true;
        }

        return false;
    }
}
