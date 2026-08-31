<?php

use App\Models\Setting\Setting;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);

    Cache::forget('global:settings');
});

it('returns an empty set when no setting row exists yet', function () {
    expect(Setting::count())->toBe(0)
        ->and(getSetting())->toBe([])
        ->and(getSetting('title'))->toBeNull();
});

it('renders the home page on a freshly migrated database', function () {
    $this->get(route('home'))->assertOk();
});

it('reads a key once the setting row is seeded', function () {
    Setting::create(['value' => ['title' => 'TopupWok']]);

    expect(getSetting('title'))->toBe('TopupWok')
        ->and(getSetting('does-not-exist'))->toBeNull();
});
