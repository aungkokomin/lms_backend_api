<?php

namespace App\Services;

use App\Models\Image;
use App\Interfaces\BundleRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use function App\Helpers\storeFile;

class BundleService
{
    protected $bundleRepositoryInterface;

    public function __construct(BundleRepositoryInterface $bundleRepositoryInterface)
    {
        $this->bundleRepositoryInterface = $bundleRepositoryInterface;
    }

    public function getAllBundles()
    {
        return $this->bundleRepositoryInterface->getAll();
    }

    public function getBundleById($id)
    {
        return $this->bundleRepositoryInterface->findById($id);
    }

    public function createBundle($data,$thumbnail,$courseIds)
    {
        try {
            $paths = storeFile($thumbnail,'bundle');

            $bundle = $this->bundleRepositoryInterface->create($data);
            $bundle->image()->create([
                'url' => $paths['url'],
            ]);
            $bundle->courses()->sync($courseIds);
            return $bundle;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());        
        }
    }

    public function updateBundle($id, $data,$thumbnail,$courseIds)
    {
        try{
            $paths = storeFile($thumbnail,'bundle');

            $bundle = $this->bundleRepositoryInterface->update($id, $data);
            $bundle->image()->update([
                'url' => $paths['url'],
            ]);
            $bundle->courses()->sync($courseIds);
            
            return $bundle->fresh();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteBundle($id)
    {
        return $this->bundleRepositoryInterface->delete($id);
    }
}
