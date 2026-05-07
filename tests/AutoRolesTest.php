<?php

use Beholdr\LaravelHelpers\Attributes\AutoRoles;
use Beholdr\LaravelHelpers\Traits\HasAutoRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

beforeEach(function () {
    config()->set('permission', require __DIR__.'/../vendor/spatie/laravel-permission/config/permission.php');
    config()->set('permission.testing', true);

    (include __DIR__.'/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub')->up();

    Schema::create('auto_role_users', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->timestamps();
    });

    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('assigns a single auto role when model is created', function () {
    Role::create(['name' => 'client']);

    $user = AutoRoleUser::create(['name' => 'Test User']);

    expect($user->hasRole('client'))->toBeTrue();
});

it('assigns multiple auto roles when model is created', function () {
    Role::create(['name' => 'client']);
    Role::create(['name' => 'editor']);

    $user = MultipleAutoRolesUser::create(['name' => 'Test User']);

    expect($user->hasAllRoles(['client', 'editor']))->toBeTrue();
});

it('assigns an auto role from backed enum when model is created', function () {
    Role::create(['name' => 'client']);

    $user = EnumAutoRoleUser::create(['name' => 'Test User']);

    expect($user->hasRole('client'))->toBeTrue();
});

it('assigns multiple auto roles from backed enums when model is created', function () {
    Role::create(['name' => 'client']);
    Role::create(['name' => 'editor']);

    $user = MultipleEnumAutoRolesUser::create(['name' => 'Test User']);

    expect($user->hasAllRoles(['client', 'editor']))->toBeTrue();
});

it('does not assign roles without AutoRoles attribute', function () {
    Role::create(['name' => 'client']);

    $user = UserWithoutAutoRoles::create(['name' => 'Test User']);

    expect($user->roles)->toBeEmpty();
});

it('requires Spatie HasRoles trait to assign auto roles', function () {
    expect(fn () => UserWithoutSpatieHasRoles::create(['name' => 'Test User']))
        ->toThrow(LogicException::class, 'must use Spatie\Permission\Traits\HasRoles');
});

#[AutoRoles('client')]
class AutoRoleUser extends Authenticatable
{
    use HasAutoRoles;
    use HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

#[AutoRoles(['client', 'editor'])]
class MultipleAutoRolesUser extends Authenticatable
{
    use HasAutoRoles;
    use HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

#[AutoRoles(AutoRole::Client)]
class EnumAutoRoleUser extends Authenticatable
{
    use HasAutoRoles;
    use HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

#[AutoRoles([AutoRole::Client, AutoRole::Editor])]
class MultipleEnumAutoRolesUser extends Authenticatable
{
    use HasAutoRoles;
    use HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

class UserWithoutAutoRoles extends Authenticatable
{
    use HasAutoRoles;
    use HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

#[AutoRoles('client')]
class UserWithoutSpatieHasRoles extends Model
{
    use HasAutoRoles;

    protected $guarded = [];

    protected $table = 'auto_role_users';
}

enum AutoRole: string
{
    case Client = 'client';
    case Editor = 'editor';
}
