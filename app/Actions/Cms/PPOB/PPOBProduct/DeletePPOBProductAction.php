<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBProduct;
use App\Traits\WithVersionedCache;

class DeletePPOBProductAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(PPOBProduct $product): ?bool
    {
        $result = $product->delete();

        $this->flushCacheGroup(CacheGroupEnum::BRANDS);

        return $result;
    }
}
