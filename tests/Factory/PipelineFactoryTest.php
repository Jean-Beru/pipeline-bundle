<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Tests\Factory;

use JeanBeru\PipelineBundle\Factory\PipelineFactory;
use League\Pipeline\PipelineInterface;
use PHPUnit\Framework\TestCase;

class PipelineFactoryTest extends TestCase
{
    public function test__invoke(): void
    {
        $stage1 = static fn (int $i) => $i + 1;
        $stage2 = static fn (int $i) => $i + 2;
        $stage3 = static fn (int $i) => $i + 3;

        $factory = new PipelineFactory();
        $pipeline = $factory([$stage1, $stage2, $stage3]);

        self::assertSame(16, $pipeline(10));
    }
}
