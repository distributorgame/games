<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBProduct;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class StorePPOBProductAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(array $data): PPOBProduct
    {
        $data['buy_price'] = currencyToNumber($data['buy_price']);
        $data['sell_price'] = currencyToNumber($data['sell_price']);

        $product = PPOBProduct::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $product,
                file: $data['image'],
                collection: 'image',
            );
        }

        $this->flushCacheGroup(CacheGroupEnum::BRANDS);

        return $product;
    }
}
