<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JeanBeru\PipelineBundle\Factory\PipelineFactory;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('jeanberu_pipeline.pipeline_factory', PipelineFactory::class)
    ;
};
