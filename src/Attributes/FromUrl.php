<?php

namespace Beholdr\LaravelHelpers\Attributes;

use Livewire\Features\SupportQueryString\BaseUrl;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class FromUrl extends BaseUrl
{
    public function dehydrate($context)
    {
        // skip dehydration

    }
}
