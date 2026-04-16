<?php

namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class isInt extends AbstractRule
{
    public function validate($input): bool
    {
        if (is_int($input)) {
            return true;
        }
        return false;
    }
}
