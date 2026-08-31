<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBProduct;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class UpdatePPOBProductAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(PPOBProduct $product, array $data): bool
    {
        $data['buy_price'] = currencyToNumber($data['buy_price']);
        $data['sell_price'] = currencyToNumber($data['sell_price']);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $product,
                file: $data['image'],
                collection: 'image',
            );
        }

        $result = $product->update($data);

        $this->flushCacheGroup(CacheGroupEnum::BRANDS);

        return $result;
    }
}
