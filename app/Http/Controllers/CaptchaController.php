<?php

namespace App\Http\Controllers;

use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    public function generate()
    {
        $captchaText = $this->captchaService->generateCaptchaText();

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

        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);

        return response($image)->header('Content-Type', 'image/png');
    }

    public function verifyCaptcha(Request $request)
    {
        $request->validate(['captcha' => 'required|string|size:6']);

        if (!$this->captchaService->verifyCaptcha($request->captcha)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect captcha. Please retry.',
                'captcha_url' => route('captcha.generate') . '?t=' . time()
            ], 400);
        }

        return response()->json(['success' => true, 'message' => 'Captcha verified successfully.']);
    }

}
