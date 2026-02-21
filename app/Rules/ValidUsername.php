<?php

namespace App\Rules;

use App\Services\NamingService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidUsername implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Check Reserved List
        if (NamingService::isReservedUsername($value)) {
            $fail('This :attribute is reserved for system use.');

            return;
        }

        // 2. Check Format (Regex)
        if (! NamingService::isValidUsername($value)) {
            $fail('The :attribute must be 1-39 characters, lowercase, alphanumeric, and cannot start or end with a hyphen.');
        }
    }
}
