<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ImageResizer
{
    private const POST_SIZE = 1080;

    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resizePostPicture(string $filePath): void
    {
        list($width, $height) = getimagesize($filePath);

        $finalBox = new Box(self::POST_SIZE, self::POST_SIZE);

        $cropPoint = new Point(0, 0);
        $cropBox = new Box($width, $height);
        if ($width > $height) {
            $cropPoint = new Point(round(($width / 2) - ($height / 2)), 0);
            $cropBox = new Box($height, $height);
        } else if ($width < $height) {
            $cropPoint = new Point(0, round(($height / 2) - ($width / 2)));
            $cropBox = new Box($width, $width);
        }

        $image = $this->imagine->open($filePath);
        $image
            ->crop($cropPoint, $cropBox)
            ->resize($finalBox)
            ->save($filePath);
    }

    public function resize(string $filename, int $resizeWidth, int $resizeHeight, bool $keepRatio = true): void
    {
        $resizedBox = new Box($resizeWidth, $resizeHeight);

        if ($keepRatio) {
            list($width, $height) = getimagesize($filename);
            $imageBox = new Box($width, $height);

            $resizedBox = self::resizeBox($imageBox, $resizeWidth, $resizeHeight);
        }

        $image = $this->imagine->open($filename);
        $image
            ->resize($resizedBox)
            ->crop(new Point(0, 0), new Box(45, 45))
            ->save($filename);
    }

    private static function resizeBox(Box $box, int $resizeWidth, int $resizeHeight): Box
    {
        $ratio = $box->getWidth() / $box->getHeight();

        $width = $resizeWidth;
        $height = $resizeHeight;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        return new Box($width, $height);
    }
}
