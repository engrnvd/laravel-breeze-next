<?php

namespace Naveed\BreezeNext\Middlewares;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class ValidateCsrfToken extends VerifyCsrfToken
{
    protected function tokensMatch($request): bool
    {
        return parent::tokensMatch($request) || $request->header('X-CSRF-KEY') === env('CSRF_KEY');
    }
}
