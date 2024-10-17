<?php

namespace Naveed\BreezeNext\Requests;

use App\Models\User;

class EmailVerificationRequest extends \Illuminate\Foundation\Auth\EmailVerificationRequest
{
    public function user($guard = null)
    {
        return User::find($this->route('id'));
    }
}
