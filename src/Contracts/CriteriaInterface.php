<?php

namespace Omatech\Lars\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    /**
     * @param Builder $q
     * @return Builder
     */
    public function apply(Builder $q): Builder;
}
