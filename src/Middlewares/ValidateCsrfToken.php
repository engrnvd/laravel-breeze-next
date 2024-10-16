<?php

namespace Naveed\BreezeNext\Middlewares;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class ValidateCsrfToken extends VerifyCsrfToken
{
    protected function tokensMatch($request): bool
    {
        return $request->header('X-CSRF-KEY') === env('BREEZE_NEXT_CSRF_KEY') || parent::tokensMatch($request);
    }
}
