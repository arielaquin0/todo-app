<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function create(array $data): bool|self
    {
        if (empty($data)) {
            return false;
        }

        $this->fillable(array_keys($data));
        $this->fill($data);

        if ($this->save() === false) {
            return false;
        }

        return $this;
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        if (empty($attributes)) {
            return false;
        }

        if (!$this->exists) {
            return false;
        }

        $this->fillable(array_keys($attributes));
        $this->fill($attributes);

        return $this->save($options);
    }

    public function findRow(array $where = [], array $field = ['*']): array|self
    {
        $query = $this->query();

        if (!empty($where)) {
            $query = $query->where($where);
        }

        $model = $query->get($field)->first();

        if (empty($model)) {
            return [];
        }

        return $model;
    }

    public function paginateRows(
        int $page = 1,
        int $perPage = 15,
        array $where = [],
        array $field = ['*'],
        array $orderBy = ['column' => 'updated_at', 'direction' => 'desc']
    ): LengthAwarePaginator {
        $query = $this->query();

        if (!empty($where)) {
            $query = $query->where($where);
        }

        $query = $query->orderBy($orderBy['column'], $orderBy['direction']);

        return $query->paginate($perPage, $field, 'page', $page);
    }

    public function findRowById(int $id, array $field = ['*']): array|self
    {
        if (empty($id)) {
            return [];
        }

        $re = $this->find($id, $field);

        if (empty($re)) {
            return [];
        }

        return $re;
    }
}
