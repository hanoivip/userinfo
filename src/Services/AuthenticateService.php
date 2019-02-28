<?php

namespace Hanoivip\Userinfo\Services;

use Illuminate\Support\Facades\Log;

class AuthenticateService
{
    public function logout($token)
    {
        Log::debug('Logout single device, token=' . $token);
    }
    
    public function logoutAllDevices()
    {
        Log::debug('Logout all devices');
    }
}