<?php

namespace Beholdr\LaravelHelpers\Attributes;

use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Embeddable extends LivewireAttribute
{
}
