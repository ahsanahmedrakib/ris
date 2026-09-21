<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Student;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function idCard(Student $student): View
    {
        $student->load(['user', 'classRoom']);

        return view('student.id-card', [
            'student' => $student,
            'qrCode' => $this->qrCodeDataUri(route('student.profile', $student)),
        ]);
    }

    public function profile(Student $student): View
    {
        $student->load(['user', 'classRoom', 'bus']);

        return view('student.profile', compact('student'));
    }

    private function qrCodeDataUri(string $data): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::H,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'scale' => 6,
            'outputBase64' => true,
            'bgColor' => [255, 255, 255],
            'drawLightModules' => true,
        ]);

        return (new QRCode($options))->render($data);
    }
}
