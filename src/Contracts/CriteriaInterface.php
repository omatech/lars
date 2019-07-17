<?php

namespace Omatech\Lars\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    /**
     * @param Model $model
     * @return mixed
     */
    public function apply(Builder $q): Builder;
}
