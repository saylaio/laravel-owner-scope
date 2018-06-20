<?php

namespace Sayla\Laravel\OwnerScope;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface OwnershipStrategy
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int $ownerId
     */
    public function apply(Builder $builder, Model $model, int $ownerId): void;
}