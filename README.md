# PipelineBundle

[![Latest Version](https://img.shields.io/github/release/Jean-Beru/pipeline-bundle.svg?style=flat-square)](https://github.com/Jean-Beru/pipeline-bundle/releases)
[![Total Downloads](https://poser.pugx.org/Jean-Beru/pipeline-bundle/downloads)](https://packagist.org/packages/Jean-Beru/pipeline-bundle)
[![Monthly Downloads](https://poser.pugx.org/Jean-Beru/pipeline-bundle/d/monthly.png)](https://packagist.org/packages/Jean-Beru/pipeline-bundle)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENCE)
[![Static analysis](https://github.com/Jean-Beru/pipeline-bundle/actions/workflows/static.yml/badge.svg?branch=main)](https://github.com/Jean-Beru/pipeline-bundle/actions/workflows/static.yml?query=branch%3Amain)
[![Tests](https://github.com/Jean-Beru/pipeline-bundle/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/Jean-Beru/pipeline-bundle/actions/workflows/tests.yml?query=branch%3Amain)

[Pipeline](https://github.com/thephpleague/pipeline) bundle for Symfony.

## Install

Install bundle :
```shell
composer require jean-beru/pipeline-bundle
`````

If you do not use [symfony/flex](https://github.com/symfony/flex), you have to add this bundle to your
`config/bundles.php` file :

```php
<?php

return [
    ...
    JeanBeru\PipelineBundle\PipelineBundle::class => ['all' => true],
];
```

## Configuration

`pipelines` defines all pipelines. The key defines pipeline's name.

`pipelines.__NAME__.stages` defines services to use as stages in this pipeline.

`pipelines.__NAME__.processor` (optional) defines a specific processor to use with this pipeline. See "What is a
processor ?" section below.

Example :
```yaml
jeanberu_pipeline:
  pipelines:
    update_stock:
      processor: 'jeanberu_pipeline.processor.event_dispatcher_processor'
      stages:
        - 'App\Stages\RetrieveProduct'
        - 'App\Stages\UpdateStockProduct'
        - 'App\Stages\PersistProduct'
    export:
      stages:
        - 'App\Stages\RetrieveProduct'
        - 'App\Stages\ExportProduct'
    some_computations:
      stages:
        - 'App\Stages\AddOne'
        - 'App\Stages\AddThree'
        - 'App\Stages\MultiplyByFour'
```

## Inject pipeline services

### Using service ID

All defined pipeline is added as a service named `jeanberu_pipeline.pipeline.__NAME__`.

With the previous configuration, we will be able to inject 3 pipelines :
- jeanberu_pipeline.pipeline.update_stock
- jeanberu_pipeline.pipeline.export
- jeanberu_pipeline.pipeline.some_computations

### Using autowiring

All defined pipeline can be autowired using its name `PipelineInterface $__NAME__Pipeline`.

With the previous configuration, we will be able to inject 3 pipelines :
- Pipeline $updateStockPipeline
- Pipeline $exportPipeline
- Pipeline $someComputationsPipeline

## Usage

Each service implements the `League\Pipeline\PipelineInterface` interface.

In this example, `some_computations` pipeline defined before will make some operations on the payload and will return
it.

```php
<?php

use League\Pipeline\PipelineInterface;

final class MyService
{
    private PipelineInterface $someComputationsPipeline;
    
    public function __construct(PipelineInterface $someComputationsPipeline)
    {
        $this->someComputationsPipeline = $someComputationsPipeline;
    }
    
    public function __invoke(int $base)
    {
        // $base = 2
        $result = $this->someComputationsPipeline($base);
        // $result = (((2 + 1) + 3) * 4) = 24
        
        // ...
    }
}
```

## What are pipeline and stage ?

A stage represents a task to execute with a payload. It must be a callable. You can implement
`League\Pipeline\StageInterface` to ensure that your stage can be called.

A pipeline represents chained stages. You can see it like a CLI command: `stage_1 | stage_2 | stage_3`. Each stage
receives the previously returned payload.
Since pipelines implements `League\Pipeline\PipelineInterface` which implements itself `League\Pipeline\StageInterface`,
you can use it as a stage to make re-usable pipelines (a.k.a. pipeline-ception). Ex: `stage_1 | pipeline_1  | stage_3`.

## What is a processor ?

To execute a pipeline, a processor is used. It must implement `League\Pipeline\ProcessorInterface`. You can use your
own service if you want to.

If `symfony/event-dispatcher` is available, a `jeanberu_pipeline.processor.event_dispatcher_processor` processor 
will be available (see [EventDispatcherProcessor](./src/Processor/EventDispatcherProcessor.php)) to dispatch some 
events :
- JeanBeru\PipelineBundle\Event\BeforeProcessorEvent
- JeanBeru\PipelineBundle\Event\BeforeStageEvent
- JeanBeru\PipelineBundle\Event\AfterStageEvent
- JeanBeru\PipelineBundle\Event\AfterProcessorEvent

## Test

Install dependencies :
```php
composer install
```

Run tests :
```php
vendor/bin/simple-phpunit
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/phpstan
```
