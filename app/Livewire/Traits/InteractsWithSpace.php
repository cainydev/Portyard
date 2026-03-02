<?php

namespace App\Livewire\Traits;

use App\Facades\CurrentSpace;
use App\Models\Space;
use Livewire\Attributes\Computed;

trait InteractsWithSpace
{
    #[Computed]
    public function currentSpace(): ?Space
    {
        return CurrentSpace::get();
    }
}
