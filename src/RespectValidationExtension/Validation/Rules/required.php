<?php
namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class required extends AbstractRule
{
    public function validate($input): bool
    {
        if (isset($input)) {
            return true;
        }
        return false;
    }
}
