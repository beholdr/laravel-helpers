<?php

namespace Beholdr\LaravelHelpers\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AutoRoles
{
    /**
     * @param  string|array<int, string>  $roles
     */
    public function __construct(
        public readonly string|array $roles,
    ) {}

    /**
     * @return array<int, string>
     */
    public function roles(): array
    {
        return is_array($this->roles) ? $this->roles : [$this->roles];
    }
}
