<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle;

use JeanBeru\PipelineBundle\DependencyInjection\PipelineExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PipelineBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        if (!$this->extension) {
            $this->extension = new PipelineExtension();
        }

        return $this->extension;
    }
}
