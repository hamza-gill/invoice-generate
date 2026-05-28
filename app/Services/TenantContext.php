<?php

namespace App\Services;

class TenantContext
{
    protected ?int $organizationId = null;

    public function set(?int $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    public function id(): ?int
    {
        return $this->organizationId;
    }

    public function clear(): void
    {
        $this->organizationId = null;
    }
}
