<?php

namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class isNumber extends AbstractRule
{
    public function validate($input): bool
    {
        if (is_numeric($input)) {
            return true;
        }

        return false;
    }
}
