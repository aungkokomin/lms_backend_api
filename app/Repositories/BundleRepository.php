<?php

namespace App\Repositories;
use App\Interfaces\BundleRepositoryInterface;

use App\Models\Bundle;

class BundleRepository implements BundleRepositoryInterface
{
    public function getAll()
    {
        return Bundle::with('courses','image')->get();
    }

    public function findById($id)
    {
        return Bundle::with('courses','image')->findOrFail($id);
    }

    public function create($data)
    {
        return Bundle::create($data);
    }

    public function update($id, $data)
    {
        $bundle = $this->findById($id);
        $bundle->update($data);
        return $bundle;
    }

    public function delete($id)
    {
        $bundle = $this->findById($id);
        $bundle->courses()->detach();
        $bundle->image()->delete();
        return $bundle->delete();
    }
}
