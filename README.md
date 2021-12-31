# PipelineBundle

[Pipeline](https://github.com/thephpleague/pipeline) bundle for Symfony.

## Install

`composer require jean-beru/pipeline-bundle`

## Configuration

```yaml
pipeline:
  pipelines:
    update_stock:
      - 'App\Stages\RetrieveProduct'
      - 'App\Stages\UpdateStockProduct'
      - 'App\Stages\PersistProduct'
    export:
      - 'App\Stages\RetrieveProduct'
      - 'App\Stages\ExportProduct'
    some_computations:
      - 'App\Stages\AddOne'
      - 'App\Stages\AddThree'
      - 'App\Stages\MultiplyByFour'
```

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
