<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository
{
    /**
     *  Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * Set model
     */
    public function setModel(): void
    {
        $this->model = app()->make($this->model());
    }

    /**
     * Set model
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get Table Name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->model->getTable();
    }

    /**
     * Get Key Name
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->model->getKeyName();
    }

    /**
     * Get By Primary Key
     *
     * @param int $id
     * @param array $relations
     *
     * @return null|Model
     */
    public function getByPK(int $id, array $relations = []): ?Model
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Get By array ids
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getByIds(array $ids): Collection
    {
        return $this->model
            ->whereIn($this->getKeyName(), $ids)
            ->get();
    }

    /**
     * Get By Foreign Key
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Collection
     */
    public function getByForeignKey(string $key, mixed $value): Collection
    {
        if (is_array($value)) {
            return $this->model
                ->whereIn($key, $value)
                ->get();
        }

        return $this->model
            ->where($key, $value)
            ->get();
    }

    /**
     * Get All
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get All With Trashed
     *
     * @return Collection
     */
    public function getAllWithTrashed(): Collection
    {
        return $this->model
            ->withTrashed()
            ->all();
    }

    /**
     * Get with conditions
     *
     * @param array $conditions
     * @param null|array $selects
     *
     * @return Collection
     */
    public function getFiltered(array $conditions, ?array $selects = null): Collection
    {
        return $this->model
            ->filter($conditions)
            ->when($selects, function ($query, $selects) {
                $query->select($selects);
            })
            ->get();
    }

    /**
     * Create new record
     *
     * @param array $values
     *
     * @return Model
     */
    public function create(array $values): Model
    {
        return $this->model->create($values);
    }

    /**
     * Update Or Create
     *
     * @param array $keys
     * @param array $values
     *
     * @return Model
     */
    public function updateOrCreate(array $keys, array $values): Model
    {
        return $this->model->updateOrCreate($keys, $values);
    }

    /**
     * Update by ID
     *
     * @param int $id
     * @param array $values
     *
     * @return null|Model
     */
    public function update(int $id, array $values): ?Model
    {
        $model = $this->getByPK($id);

        if (is_null($model)) {
            return null;
        }

        $model
            ->fill($values)
            ->save();

        return $model;
    }

    /**
     * Update by Model
     *
     * @param Model $model
     * @param array $values
     *
     * @return null|Model
     *
     * @throws Exception
     */
    public function updateByModel(Model $model, array $values): ?Model
    {
        if (! $model instanceof $this->model) {
            throw new Exception('Type mismatch between Repo and Argument.');
        }

        $model
            ->fill($values)
            ->save();

        return $model;
    }

    /**
     * Update multiple records by Ids
     *
     * @param array $ids
     * @param array $values
     *
     * @return int
     */
    public function updateByIds(array $ids, array $values): int
    {
        return $this->model
            ->whereIn('id', $ids)
            ->update($values);
    }

    /**
     * Destroy
     *
     * @param int $id
     *
     * @return null|bool
     */
    public function destroy(int $id): ?bool
    {
        $data = $this->getByPK($id);

        if (is_null($data)) {
            return false;
        }

        return $data->delete();
    }

    /**
     * Destroy by conditions
     *
     * @param array $conditions
     *
     * @return int
     */
    public function destroyWhere(array $conditions): int
    {
        return $this->model
            ->where($conditions)
            ->delete();
    }

    /**
     * Destroy by list of ids
     *
     * @param array $ids
     *
     * @return int
     */
    public function destroyByIds(array $ids): int
    {
        return $this->model
            ->whereIn('id', $ids)
            ->delete();
    }

    /**
     * @param array $conditions
     * @param array $selects
     *
     * @return LengthAwarePaginator
     */
    public function getFilteredPaginator(array $conditions, array $selects = []): LengthAwarePaginator
    {
        if (empty($selects)) {
            return $this->model
                ->filter($conditions)
                ->paginateFilter();
        }

        return $this->model
            ->select($selects)
            ->filter($conditions)
            ->paginateFilter();
    }

    public function truncate()
    {
        return $this->model->truncate();
    }
}
