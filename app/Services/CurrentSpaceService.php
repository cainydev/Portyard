<?php

namespace App\Services;

use App\Models\Space;

class CurrentSpaceService
{
    protected ?Space $space = null;

    /**
     * Set the current active space.
     */
    public function set(Space $space): void
    {
        $this->space = $space;
    }

    /**
     * Get the current active space instance.
     */
    public function get(): ?Space
    {
        return $this->space;
    }

    /**
     * Check if a space is currently active.
     */
    public function check(): bool
    {
        return $this->space !== null;
    }

    /**
     * Get the ID of the current space.
     */
    public function id(): int|string|null
    {
        return $this->space?->id;
    }
}
