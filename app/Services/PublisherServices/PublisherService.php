<?php

namespace App\Services\PublisherServices;

use App\Models\Publisher;
use Illuminate\Support\Facades\DB;

class PublisherService
{
    public function publisher(array $param = [], ?int $publisherId = null)
    {
        return $publisherId
            ? $this->getPublisher($publisherId)
            : $this->getPublishers($param);
    }

    public function getPublishers(array $param = [])
    {
        $query = Publisher::query();

        if (isset($param['name'])) {
            $query->where('name', 'like', '%' . $param['search'] . '%');
        }

        return $query->paginate(15);
    }

    public function getPublisher(int $publisherId, array $param = [])
    {
        $query = Publisher::query();

        $query->where('id', $publisherId);

        return $query->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Publisher::create($data);
        });
    }

    public function update(string $publisherId, array $data)
    {
        return DB::transaction(function () use ($publisherId, $data) {
            return Publisher::where('id', $publisherId)->update($data);
        });
    }

    public function delete(string $publisherId)
    {
        return DB::transaction(function () use ($publisherId) {
            return Publisher::where('id', $publisherId)->delete();
        });
    }
}
