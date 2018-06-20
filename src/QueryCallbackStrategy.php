<?php

namespace Sayla\Laravel\OwnerScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QueryCallbackStrategy implements OwnershipStrategy
{
    protected $queryBuilderCallback;

    public function __construct(\Closure $queryBuilderCallback)
    {
        $this->queryBuilderCallback = $queryBuilderCallback;
    }

    public function apply(Builder $builder, Model $model, int $ownerId): void
    {
        call_user_func($this->queryBuilderCallback, $builder, $model, $ownerId);
    }
}
