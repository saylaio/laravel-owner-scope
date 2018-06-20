<?php

namespace Sayla\Laravel\OwnerScope\Scope;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Gate as GateFacade;
use Sayla\Laravel\OwnerScope\BaseGuardedScope;
use Sayla\Laravel\OwnerScope\ColumnOwnershipStrategy;
use Sayla\Laravel\OwnerScope\OwnershipStrategy;
use Sayla\Laravel\OwnerScope\QueryCallbackStrategy;

class GatedScope extends BaseGuardedScope
{
    /** @var \Illuminate\Support\Facades\Gate */
    protected $gate;
    protected $requiredAbilities = [];
    protected $abilities = [];

    public function __construct(OwnershipStrategy $strategy, Guard $guard, Gate $gate)
    {
        $this->strategy = $strategy;
        $this->guard = $guard;
        $this->gate = $gate->forUser($this->getUser());
    }

    public static function scopeBy(\Closure $closure, $guard = null, $gate = null)
    {
        return new static(
            new QueryCallbackStrategy($closure),
            self::resolveGuard($guard),
            $gate ?? GateFacade::getFacadeRoot()
        );
    }

    public static function scopeByColumn($column = 'user_id', $guard = null, $gate = null)
    {
        return new static(
            new ColumnOwnershipStrategy($column),
            self::resolveGuard($guard),
            $gate ?? GateFacade::getFacadeRoot()
        );
    }

    public function can(string ...$ability)
    {
        $this->abilities = array_merge($this->abilities, $ability);
        return $this;
    }

    public function must(string ...$ability)
    {
        $this->requiredAbilities = array_merge($this->requiredAbilities, $ability);
        return $this;
    }

    /**
     * @return bool
     */
    protected function userIsAuthorized($arguments = []): bool
    {
        if (count($this->requiredAbilities) > 0) {
            if (!$this->gate->check($this->requiredAbilities, $arguments)) {
                return false;
            }
        }
        if (count($this->abilities) > 0) {
            if (!$this->gate->any($this->abilities, $arguments)) {
                return false;
            }
        }
        return true;
    }

}
