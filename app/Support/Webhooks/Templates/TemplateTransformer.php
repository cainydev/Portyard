<?php

namespace App\Support\Webhooks\Templates;

interface TemplateTransformer
{
    /**
     * Transform the generic Portyard payload into a vendor-specific shape.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function transform(array $payload): array;
}
