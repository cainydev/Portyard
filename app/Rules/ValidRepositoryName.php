<?php

namespace App\Rules;

use App\Services\NamingService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidRepositoryName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Fast path: If the service is happy, we are happy.
        if (NamingService::isValidRepositoryName($value)) {
            return;
        }

        // Length Check
        if (strlen($value) < 1 || strlen($value) > 100) {
            $fail('The :attribute must be between 1 and 100 characters.');

            return;
        }

        // Start/End Check
        if (preg_match('/^[._-]|[._-]$/', $value)) {
            $fail('The :attribute cannot start or end with a special character.');

            return;
        }

        // Base Character Check
        if (! preg_match(NamingService::repositoryNameRegex(), $value)) {
            $fail('The :attribute can only contain lowercase letters, numbers, dots, hyphens, and underscores.');

            return;
        }

        // Consecutive Separator Check
        if (preg_match('/(\.\.|--|\.-|-\.|__)/', $value)) {
            $fail('The :attribute cannot contain consecutive or mixed separators (e.g., "..", "--").');

            return;
        }

        // Fallback
        $fail('The :attribute format is invalid.');
    }
}
