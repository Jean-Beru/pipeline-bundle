<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Tests\DependencyInjection;

use JeanBeru\PipelineBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testConfigTree(): void
    {
        $options = [
            'pipelines' => [
                'pipeline_one' => [
                    'processor' => 'My\Custom\Processor',
                    'stages' => [
                        'service_one',
                        'service_two',
                    ],
                ],
                'pipeline_two' => [
                    'stages' => [
                        'service_one',
                        'service_three',
                    ],
                ],
            ],
        ];

        $expected = [
            'pipelines' => [
                'pipeline_one' => [
                    'processor' => 'My\Custom\Processor',
                    'stages' => [
                        'service_one',
                        'service_two',
                    ],
                ],
                'pipeline_two' => [
                    'processor' => null,
                    'stages' => [
                        'service_one',
                        'service_three',
                    ],
                ],
            ],
        ];

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, [$options]);

        $this->assertEquals($expected, $config);
    }
}
