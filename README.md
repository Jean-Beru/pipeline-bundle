# PipelineBundle

[Pipeline](https://github.com/thephpleague/pipeline) bundle for Symfony.

## Install

`composer require jean-beru/pipeline-bundle`

## Configuration

```yaml
pipeline:
  pipelines:
    update_stock:
      processor: 'jean_beru_pipeline.processor.event_dispatcher_processor'
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

If `symfony/event-dispatcher` is available in `--no-dev` mode, a `jean_beru_pipeline.processor.
event_dispatcher_processor` will be available (see 
[EventDispatcherProcessor](./src/Processor/EventDispatcherProcessor.php)) to dispatch some events :
- JeanBeru\PipelineBundle\Event\BeforeProcessorEvent
- JeanBeru\PipelineBundle\Event\BeforeStageEvent
- JeanBeru\PipelineBundle\Event\AfterStageEvent
- JeanBeru\PipelineBundle\Event\AfterProcessorEvent

## Inject pipeline services

### Using service ID

All defined pipeline is added as a service named `jean_beru_pipeline.pipeline.__NAME__`.

With the previous configuration, we will be able to inject 3 pipelines :
- jean_beru_pipeline.pipeline.update_stock
- jean_beru_pipeline.pipeline.export
- jean_beru_pipeline.pipeline.some_computations

### Using autowiring

All defined pipeline can be autowired using its name `PipelineInterface $__NAME__Pipeline`.

With the previous configuration, we will be able to inject 3 pipelines :
- Pipeline $updateStockPipeline
- Pipeline $exportPipeline
- Pipeline $someComputationsPipeline

## Usage

Each service implements the `League\Pipeline\PipelineInterface` interface.

In this example, stages defined in `some_computations` pipeline make some operations on the payload and returns it.

```php

use League\Pipeline\PipelineInterface;

final class MyService
{
    private PipelineInterface $someComputationsPipeline;
    
    public function __construct(PipelineInterface $someComputationsPipeline)
    {
        $this->someComputationsPipeline = $someComputationsPipeline;
    }
    
    public function __invoke(int $base): Response
    {
        // $base = 2
        $result = $this->someComputationsPipeline($base);
        // $result = (((2 + 1) + 3) * 4) = 24
    }
}
```

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
