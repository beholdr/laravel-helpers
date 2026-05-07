<?php

namespace Beholdr\LaravelHelpers\Attributes;

use BackedEnum;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AutoRoles
{
    /**
     * @param  string|BackedEnum|array<int, string|BackedEnum>  $roles
     */
    public function __construct(
        public readonly string|BackedEnum|array $roles,
    ) {}

    /**
     * @return array<int, string|BackedEnum>
     */
    public function roles(): array
    {
        return is_array($this->roles) ? $this->roles : [$this->roles];
    }
}
