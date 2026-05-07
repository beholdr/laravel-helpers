<?php

namespace Beholdr\LaravelHelpers\Traits;

use Beholdr\LaravelHelpers\Attributes\AutoRoles;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use ReflectionClass;

/**
 * @phpstan-require-extends Model
 *
 * @mixin Model
 */
trait HasAutoRoles
{
    public static function bootHasAutoRoles(): void
    {
        static::created(function (Model $model): void {
            $roles = static::autoRoles();

            if ($roles === []) {
                return;
            }

            if (! method_exists($model, 'assignRole')) {
                throw new LogicException(sprintf(
                    '%s must use Spatie\\Permission\\Traits\\HasRoles to use %s.',
                    $model::class,
                    self::class,
                ));
            }

            $model->assignRole($roles);
        });
    }

    /**
     * @return array<int, string|\BackedEnum>
     */
    protected static function autoRoles(): array
    {
        $attribute = (new ReflectionClass(static::class))->getAttributes(AutoRoles::class)[0] ?? null;

        if ($attribute === null) {
            return [];
        }

        return $attribute->newInstance()->roles();
    }
}
