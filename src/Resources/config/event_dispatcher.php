<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JeanBeru\PipelineBundle\Processor\EventDispatcherProcessor;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('jeanberu_pipeline.processor.event_dispatcher_processor', EventDispatcherProcessor::class)
            ->args([
                service('event_dispatcher')
            ])
    ;
};
