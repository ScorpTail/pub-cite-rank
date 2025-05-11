<?php

namespace App\Services\WeightServices;

use App\Models\Weight;
use Illuminate\Support\Facades\DB;

class WeightService
{
    public function weight(array $param = [], ?int $weightId = null)
    {
        return $weightId
            ? $this->getWeight($weightId)
            : $this->getWeights();
    }

    public function getWeights(array $param = [])
    {
        $query = Weight::query();

        return $query->get();
    }

    public function getWeight(int $weightId, array $param = [])
    {
        $query = Weight::query();

        $query->where('id', $weightId);

        return $query->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Weight::create($data);
        });
    }

    public function update(string $weightId, array $data)
    {
        return DB::transaction(function () use ($weightId, $data) {
            return Weight::where('id', $weightId)->update($data);
        });
    }

    public function delete(string $weightId)
    {
        return DB::transaction(function () use ($weightId) {
            return Weight::where('id', $weightId)->delete();
        });
    }
}
