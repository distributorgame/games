<?php

namespace App\Actions\Cms\PPOB\PPOBCategory;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBCategory;
use App\Traits\WithVersionedCache;

class DeletePPOBCategoryAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(PPOBCategory $category): ?bool
    {
        $result = $category->delete();

        $this->flushCacheGroup(CacheGroupEnum::CATEGORIES, CacheGroupEnum::BRANDS);

        return $result;
    }
}
