<?php

namespace App\Actions\Cms\Web\Faq;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Faq;
use App\Traits\WithVersionedCache;

class DeleteFaqAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(Faq $faq): ?bool
    {
        $result = $faq->delete();

        $this->flushCacheGroup(CacheGroupEnum::FAQS);

        return $result;
    }
}
