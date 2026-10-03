<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Rate limiters share the default cache store and every test runs from
        // 127.0.0.1, so a handful of tests submitting "public" forms would
        // otherwise throttle each other and fail for the wrong reason.
        RateLimiter::clear('login');
        RateLimiter::clear('login-ip');
        RateLimiter::clear('api-login');
        RateLimiter::clear('api-login-ip');
        RateLimiter::clear('admission');
        RateLimiter::clear('admission-day');
        RateLimiter::clear('admission-preview');
        RateLimiter::clear('admission-preview-day');
        RateLimiter::clear('contact');
        RateLimiter::clear('public-form');
        RateLimiter::clear('api');
        RateLimiter::clear('password');
        RateLimiter::clear('export');
        RateLimiter::clear('export-day');
        RateLimiter::clear('destructive');

        Cache::clear();
    }
}
