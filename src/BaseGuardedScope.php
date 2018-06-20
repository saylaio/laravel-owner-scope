<?php

namespace Sayla\Laravel\OwnerScope;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

abstract class BaseGuardedScope extends BaseOwnerScope
{
    /** @var bool */
    protected $authorizeGuestFlag = true;
    /** @var Guard */
    protected $guard;

    protected static function resolveGuard($guard = null)
    {
        if (is_string($guard)) {
            $guard = Auth::guard($guard);
        } else {
            $guard = $guard ?? Auth::guard();
        }
        return $guard;
    }

    /**
     * @return $this
     */
    public function authorizeGuestUser()
    {
        $this->authorizeGuestFlag = true;
        return $this;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Database\Eloquent\Model
     */
    protected function getUser()
    {
        return $this->guard->user();
    }

    /**
     * @return bool
     */
    public function isOwnerAuthorized(): bool
    {
        if ($this->isGuestUser()) {
            return $this->authorizeGuestFlag;
        }

        return $this->userIsAuthorized();
    }

    /**
     * @return int
     */
    protected function resolveOwnerId(): int
    {
        return $this->guard->id();
    }

    protected function isGuestUser(): bool
    {
        return $this->guard->guest();
    }

    /**
     * @return bool
     */
    protected abstract function userIsAuthorized(): bool;

    /**
     * @return $this
     */
    public function unauthorizeGuestUser()
    {
        $this->authorizeGuestFlag = false;
        return $this;
    }
}
