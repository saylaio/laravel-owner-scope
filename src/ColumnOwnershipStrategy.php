<?php

namespace Sayla\Laravel\OwnerScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ColumnOwnershipStrategy implements OwnershipStrategy
{
    protected $userIdColumn;

    public function __construct(string $column)
    {
        $this->userIdColumn = $column;
    }

    public function apply(Builder $builder, Model $model, int $ownerId): void
    {
        $builder->where($this->userIdColumn, '=', $ownerId);
    }

    /**
     * @param string $userIdColumn
     * @return $this
     */
    public function setUserIdColumn(string $userIdColumn)
    {
        $this->userIdColumn = $userIdColumn;
        return $this;
    }

}
