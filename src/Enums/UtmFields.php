<?php

namespace Beholdr\LaravelHelpers\Enums;

enum UtmFields: string
{
    case UTM_CAMPAIGN = 'utm_campaign';
    case UTM_CONTENT = 'utm_content';
    case UTM_MEDIUM = 'utm_medium';
    case UTM_SOURCE = 'utm_source';
    case UTM_TERM = 'utm_term';

    public static function fromQuery(?string $query = null): array
    {
        parse_str(parse_url($query, PHP_URL_QUERY) ?? $query, $vars);

        if (empty($vars)) {
            return [];
        }

        $data = [];

        foreach (self::cases() as $utmField) {
            $key = $utmField->value;

            if (isset($vars[$key])) {
                $data[$key] = $vars[$key];
            }
        }

        return $data;
    }
}
