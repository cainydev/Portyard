<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum WebhookTrigger: string
{
    use EnumToArray;

    case TagPushed = 'tag_pushed';
    case TagUpdated = 'tag_updated';

    public function getReadableName(): string
    {
        return match ($this) {
            self::TagPushed => 'Tag pushed',
            self::TagUpdated => 'Tag updated',
        };
    }
}
