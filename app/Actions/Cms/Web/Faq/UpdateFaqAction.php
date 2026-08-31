<?php

namespace App\Actions\Cms\Web\Faq;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Faq;
use App\Traits\WithVersionedCache;

class UpdateFaqAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(Faq $faq, array $data): bool
    {
        $result = $faq->update($data);

        $this->flushCacheGroup(CacheGroupEnum::FAQS);

        return $result;
    }
}
