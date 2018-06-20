<?php

namespace Sayla\Laravel\OwnerScope\Scope\Spatie;

use Illuminate\Contracts\Auth\Guard;
use Sayla\Laravel\OwnerScope\BaseGuardedScope;
use Sayla\Laravel\OwnerScope\ColumnOwnershipStrategy;
use Sayla\Laravel\OwnerScope\OwnershipStrategy;
use Sayla\Laravel\OwnerScope\QueryCallbackStrategy;

/**
 * @see https://github.com/spatie/laravel-permission
 */
class SpatiePermissionScope extends BaseGuardedScope
{
    protected $permissions = [];
    protected $requiredPermissions = [];

    public function __construct(OwnershipStrategy $strategy, Guard $guard)
    {
        $this->strategy = $strategy;
        $this->guard = $guard;
    }

    public static function scopeBy(\Closure $closure, $guard = null)
    {
        return new static(new QueryCallbackStrategy($closure), self::resolveGuard($guard));
    }

    public static function scopeByColumn($column = 'user_id', $guard = null)
    {
        return new static(new ColumnOwnershipStrategy($column), self::resolveGuard($guard));
    }

    /**
     * @param string ...$permission
     * @return $this
     */
    public function can(string ...$permission)
    {
        $this->permissions = array_merge($this->permissions, $permission);
        return $this;
    }

    /**
     * @param string ...$permission
     * @return $this
     */
    public function must(string ...$permission)
    {
        $this->requiredPermissions = array_merge($this->requiredPermissions, $permission);
        return $this;
    }

    /**
     * @return bool
     */
    protected function userIsAuthorized(): bool
    {
        if (count($this->requiredPermissions) > 0) {
            if (!$this->getUser()->hasAllPermissions($this->requiredPermissions)) {
                return false;
            }
        }

        if (count($this->permissions) > 0) {
            if (!$this->getUser()->hasAnyPermission($this->permissions)) {
                return false;
            }
        }
        return true;
    }
}
