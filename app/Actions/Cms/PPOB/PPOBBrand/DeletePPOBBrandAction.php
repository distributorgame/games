<?php

namespace App\Actions\Cms\PPOB\PPOBBrand;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBBrand;
use App\Traits\WithVersionedCache;

class DeletePPOBBrandAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(PPOBBrand $brand): ?bool
    {
        $result = $brand->delete();

        $this->flushCacheGroup(CacheGroupEnum::BRANDS, CacheGroupEnum::CATEGORIES);

        return $result;
    }
}
