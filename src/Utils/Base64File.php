<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\File;

class Base64File extends File
{
    public function __construct(string $base64Content, string $originalName = 'Base64File')
    {
        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
        $data = base64_decode($this->getBase64String($base64Content));
        file_put_contents($filePath, $data);

        parent::__construct($filePath, $originalName);
    }

    private function getBase64String(string $base64Content): string
    {
        $data = explode(';base64,', $base64Content);

        return count($data) > 1 ? $data[1] : $data[0];
    }
}
