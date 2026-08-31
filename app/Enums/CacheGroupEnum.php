<?php

namespace App\Enums;

enum CacheGroupEnum: string
{
    case SLIDERS = 'sliders';
    case CATEGORIES = 'categories';
    case BRANDS = 'brands';
    case FAQS = 'faqs';

    public function label(): string
    {
        return match ($this) {
            self::SLIDERS => 'Sliders',
            self::CATEGORIES => 'Categories',
            self::BRANDS => 'Brands',
            self::FAQS => 'FAQs',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
