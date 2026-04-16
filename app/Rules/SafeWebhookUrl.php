<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SafeWebhookUrl implements ValidationRule
{
    /**
     * Validate that a webhook URL is well-formed and not pointed at
     * loopback, private, or link-local addresses in production.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid URL.');

            return;
        }

        $parts = parse_url($value);

        if ($parts === false || empty($parts['host']) || empty($parts['scheme'])) {
            $fail('The :attribute must be a valid URL.');

            return;
        }

        $scheme = strtolower($parts['scheme']);
        $allowedSchemes = app()->isProduction() ? ['https'] : ['http', 'https'];

        if (! in_array($scheme, $allowedSchemes, true)) {
            $fail('The :attribute must use HTTPS.');

            return;
        }

        if (! app()->isProduction()) {
            return;
        }

        $host = $parts['host'];

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if ($this->isBlockedIp($host)) {
                $fail('The :attribute cannot target private or loopback addresses.');
            }

            return;
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA);

        if (empty($records)) {
            $fail('The :attribute host could not be resolved.');

            return;
        }

        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;

            if ($ip && $this->isBlockedIp($ip)) {
                $fail('The :attribute cannot target private or loopback addresses.');

                return;
            }
        }
    }

    private function isBlockedIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
