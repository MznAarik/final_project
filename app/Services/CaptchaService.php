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

    public function generate()
    {
        $captchaText = $this->generateCaptchaText();

        $width = 180;
        $height = 60;

        $image = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        for ($i = 0; $i < 300; $i++) {
            $noiseColor = imagecolorallocate($image, rand(150, 200), rand(150, 200), rand(150, 200));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
        }

        for ($i = 0; $i < 10; $i++) {
            $lineColor = imagecolorallocate($image, rand(120, 180), rand(120, 180), rand(120, 180));
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        $x = 15;
        for ($i = 0; $i < strlen($captchaText); $i++) {
            $angle = rand(-25, 25);
            $fontSize = rand(20, 26);
            $fontColor = imagecolorallocate($image, rand(10, 100), rand(10, 100), rand(10, 100));

            imagettftext(
                $image,
                $fontSize,
                $angle,
                $x + rand(0, 3),
                rand(35, 55),
                $fontColor,
                public_path('fonts/arial.ttf'),
                $captchaText[$i]
            );

            $x += 25;
        }

        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return response($pngData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function verifyCaptcha($input)
    {
        $stored = Session::get('captcha');
        return strtolower($input) === strtolower($stored);
    }
}
