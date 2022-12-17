<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ImageResizer
{
    private const POST_SIZE = 1080;
    private const AVATAR_SIZE = 300;

    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resizePostPicture(string $filePath): void
    {
        $this->cropToSquare($filePath, self::POST_SIZE);
    }

    public function resizeUserAvatar(string $filePath): void
    {
        $this->cropToSquare($filePath, self::AVATAR_SIZE);
    }

    public function cropToSquare(string $filePath, int $sideSize): void
    {
        list($width, $height) = getimagesize($filePath);

        $finalBox = new Box($sideSize, $sideSize);

        $cropPoint = new Point(0, 0);
        $cropBox = new Box($width, $height);
        if ($width > $height) {
            $cropPoint = new Point(round(($width / 2) - ($height / 2)), 0);
            $cropBox = new Box($height, $height);
        } elseif ($width < $height) {
            $cropPoint = new Point(0, round(($height / 2) - ($width / 2)));
            $cropBox = new Box($width, $width);
        }

        $image = $this->imagine->open($filePath);
        $image
            ->crop($cropPoint, $cropBox)
            ->resize($finalBox)
            ->save($filePath);
    }
}
