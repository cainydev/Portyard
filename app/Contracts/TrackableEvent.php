<?php

namespace App\Contracts;

use App\Enums\Action;
use Illuminate\Database\Eloquent\Model;

interface TrackableEvent
{
    public function action(): Action;

    public function subject(): ?Model;

    public function spaceId(): ?string;

    public function properties(): array;
}
