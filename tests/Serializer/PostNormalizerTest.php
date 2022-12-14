<?php

namespace App\Tests\Serializer;

use App\Serializer\PostNormalizer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostNormalizerTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testSomething()
    {
        self::bootKernel();

        $container = static::getContainer();

        $postNormalizer = $container->get(PostNormalizer::class);
//        $result = $postNormalizer->normalize();

//        $this->assertEquals('...', $result);
    }
}
