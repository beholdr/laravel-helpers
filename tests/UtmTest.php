<?php

use Beholdr\LaravelHelpers\Enums\UtmFields;

it('parse from query', function () {
    $query = 'utm_campaign=campaign&utm_content=content&utm_medium=medium&utm_source=source&utm_term=term';

    expect(UtmFields::fromQuery($query))->toBe([
        'utm_campaign' => 'campaign',
        'utm_content' => 'content',
        'utm_medium' => 'medium',
        'utm_source' => 'source',
        'utm_term' => 'term',
    ]);

    expect(UtmFields::fromQuery('?'.$query))->toBe([
        'utm_campaign' => 'campaign',
        'utm_content' => 'content',
        'utm_medium' => 'medium',
        'utm_source' => 'source',
        'utm_term' => 'term',
    ]);
});

it('parse from incomplete query', function () {
    expect(UtmFields::fromQuery('utm_campaign=campaign&utm_content=content&utm_other=other'))->toBe([
        'utm_campaign' => 'campaign',
        'utm_content' => 'content',
    ]);
});

it('parse from empty query', function () {
    expect(UtmFields::fromQuery())->toBe([]);
    expect(UtmFields::fromQuery(''))->toBe([]);
});
