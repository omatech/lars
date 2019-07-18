<?php

namespace Omatech\Lars;

use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Omatech\Lars\Contracts\CriteriaInterface;
use Omatech\LaravelStatusable\App\Models\StatusHistory;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    private $model;

    /**
     * Collection of Criterias
     *
     * @var Collection
     */
    private $criterias;

    /**
     * @return mixed
     */
    abstract public function model(): String;

    /**
     *
     */
    public function __construct()
    {
        $this->criterias = new Collection();
    }

    /**
     * @return Builder
     */
    private function makeModel() : Builder
    {
        $model = app()->make($this->model());

        if (!$model instanceof Model) {
            throw new RuntimeException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model->newQuery();
    }

    public function query(): Builder
    {
        $this->model = $this->makeModel();

        $this->applyCriteria();

        return $this->model;
    }

    public function getCriterias()
    {
        return $this->criterias;
    }

    public function pushCriteria(CriteriaInterface $criteria)
    {
        $this->criterias->push($criteria);
        return $this;
    }

    public function popCriteria(CriteriaInterface $criteria)
    {
        $this->criterias = $this->criterias->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }
            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }
            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();
        return $this;
    }

    public function applyCriteria()
    {
        $criterias = $this->getCriterias() ?? [];

        foreach ($criterias as $criteria) {
            $this->model = $criteria->apply($this->model);
        }
    }
}
