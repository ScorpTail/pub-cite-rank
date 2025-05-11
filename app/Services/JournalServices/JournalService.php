<?php

namespace App\Services\JournalServices;

use App\Models\Journal;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function journal(array $param = [], ?int $journalId = null)
    {
        return $journalId
            ? $this->getJournal($journalId)
            : $this->getJournals();
    }

    public function getJournals(array $param = [])
    {
        $query = Journal::query();

        return $query->get();
    }

    public function getJournal(int $journalId, array $param = [])
    {
        $query = Journal::query();

        $query->where('id', $journalId);

        return $query->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Journal::create($data);
        });
    }

    public function update(string $journalId, array $data)
    {
        return DB::transaction(function () use ($journalId, $data) {
            return Journal::where('id', $journalId)->update($data);
        });
    }

    public function delete(string $journalId)
    {
        return DB::transaction(function () use ($journalId) {
            return Journal::where('id', $journalId)->delete();
        });
    }
}
