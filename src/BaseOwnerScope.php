<?php

namespace Sayla\Laravel\OwnerScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

abstract class BaseOwnerScope implements Scope
{
    /** @var \Sayla\Laravel\OwnerScope\OwnershipStrategy */
    protected $strategy;
    /** @var  int */
    private $ownerId;
    /** @var bool */
    private $authorizationFlag = false;

    /**
     * Apply the scope to a given Eloquent query builder when the owner should not have access
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->authorizationFlag === null) {
            // always apply strategy
            $this->strategy->apply($builder, $model, $this->getOwnerId());
        } elseif ($this->authorizationFlag === $this->isOwnerAuthorized()) {
            $this->strategy->apply($builder, $model, $this->getOwnerId());
        }
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        if ($this->ownerId === null) {
            return $this->resolveOwnerId();
        }
        return $this->ownerId;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setOwnerId(int $id)
    {
        $this->ownerId = $id;
        return $this;
    }

    /**
     * @return int
     */
    protected abstract function resolveOwnerId(): int;

    /**
     * @return bool
     */
    public abstract function isOwnerAuthorized(): bool;

    /**
     * Applies strategy without regard for owner's authorization status
     * "Because there is an owner, apply strategy"
     *
     * @return $this
     */
    public function applyAlways()
    {
        $this->authorizationFlag = null;
        return $this;
    }

    /**
     * Applies strategy when owner is authorized.
     * "Because owner can do XYZ, apply strategy"
     *
     * @return $this
     */
    public function applyWhenAuthorized()
    {
        $this->authorizationFlag = true;
        return $this;
    }

    /**
     * Applies strategy when owner is unauthorized.
     * "Because owner can NOT do XYZ, apply strategy"
     *
     * @return $this
     */
    public function applyWhenUnauthorized()
    {
        $this->authorizationFlag = false;
        return $this;
    }
}
