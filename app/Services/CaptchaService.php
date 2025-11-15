<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CaptchaService
{
    public function generateCaptchaText($length = 6)
    {
        $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $captchaText = substr(str_shuffle($charset), 0, $length);

        Session::put('captcha', $captchaText);

        return $captchaText;
    }

    public function verifyCaptcha($input)
    {
        $storedCaptcha = Session::get('captcha');
        return strtolower($input) === strtolower($storedCaptcha);
    }
}
