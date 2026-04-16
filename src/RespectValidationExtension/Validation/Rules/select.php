<?php
namespace wwaz\RespectValidationExtension\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class select extends AbstractRule
{
    /**
     * Options.
     *
     * @var array
     */
    protected $options;

    /**
     * Initializes the rule.
     *
     * @param mixed[] $needles At least one of the values provided must be found in input string or array
     * @param bool $identical Defines whether the value should be compared strictly, when validating array
     */
    public function __construct($options)
    {
        $this->options = func_get_args();
    }

    public function validate($input): bool
    {
        if (in_array($input, $this->options)) {
            return true;
        }
        return false;
    }
}
