# Server-Timing Implementation Options

> **Issue:** [#24 - Server Timing](https://github.com/contributte/application/issues/24)
> **Goal:** Place break-points measuring and send it with Server-Timing header

## Overview

The [Server-Timing](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Server-Timing) HTTP header allows servers to communicate backend performance metrics to clients. These metrics appear in browser DevTools and can be accessed via the `PerformanceServerTiming` API.

**Header Format:**
```
Server-Timing: metric-name;dur=duration-ms;desc="Description"
Server-Timing: db;dur=53, app;dur=47.2, cache;desc="Cache Read";dur=23.2
```

---

## Option 1: Standalone ServerTiming Service Class

**Approach:** Create an independent service class that collects metrics and can be injected anywhere.

### Structure
```
src/
└── ServerTiming/
    ├── ServerTiming.php          # Main service (metrics collection)
    ├── Metric.php                # Value object for single metric
    └── ServerTimingExtension.php # DI extension (optional)
```

### Implementation

```php
<?php declare(strict_types = 1);

namespace Contributte\Application\ServerTiming;

final class Metric
{
    public function __construct(
        public readonly string $name,
        public readonly ?float $duration = null,
        public readonly ?string $description = null,
    ) {}

    public function toString(): string
    {
        $parts = [$this->name];
        if ($this->description !== null) {
            $parts[] = sprintf('desc="%s"', addslashes($this->description));
        }
        if ($this->duration !== null) {
            $parts[] = sprintf('dur=%.2f', $this->duration);
        }
        return implode(';', $parts);
    }
}

final class ServerTiming
{
    /** @var array<string, Metric> */
    private array $metrics = [];

    /** @var array<string, float> */
    private array $startTimes = [];

    public function start(string $name): void
    {
        $this->startTimes[$name] = hrtime(true);
    }

    public function stop(string $name, ?string $description = null): void
    {
        if (!isset($this->startTimes[$name])) {
            return;
        }
        $duration = (hrtime(true) - $this->startTimes[$name]) / 1e6; // Convert to ms
        $this->metrics[$name] = new Metric($name, $duration, $description);
        unset($this->startTimes[$name]);
    }

    public function add(string $name, ?float $duration = null, ?string $description = null): void
    {
        $this->metrics[$name] = new Metric($name, $duration, $description);
    }

    public function getHeaderValue(): string
    {
        return implode(', ', array_map(fn(Metric $m) => $m->toString(), $this->metrics));
    }

    public function apply(HttpResponse $response): void
    {
        if (count($this->metrics) > 0) {
            $response->setHeader('Server-Timing', $this->getHeaderValue());
        }
    }
}
```

### Usage

```php
// In presenter
public function __construct(
    private ServerTiming $serverTiming,
) {}

public function actionDefault(): void
{
    $this->serverTiming->start('db');
    $data = $this->database->query('...');
    $this->serverTiming->stop('db', 'Database query');

    $this->serverTiming->add('cache', 12.5, 'Cache lookup');
}

public function afterRender(): void
{
    $this->serverTiming->apply($this->getHttpResponse());
}
```

### Pros
- Simple and focused single-responsibility class
- Easy to understand and use
- Can be used anywhere (presenters, services, middleware)
- No framework coupling beyond Nette HTTP interfaces

### Cons
- Requires manual `apply()` call to set header
- No automatic integration with request lifecycle

---

## Option 2: Response Decorator / Wrapper

**Approach:** Create a decorator that wraps any `Nette\Application\Response` and adds Server-Timing header.

### Structure
```
src/
└── Response/
    └── ServerTimingResponse.php  # Response decorator with timing
```

### Implementation

```php
<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

final class ServerTimingResponse implements Response
{
    /** @var array<string, array{duration: ?float, description: ?string}> */
    private array $metrics = [];

    public function __construct(
        private Response $innerResponse,
    ) {}

    public function addMetric(string $name, ?float $duration = null, ?string $description = null): self
    {
        $this->metrics[$name] = ['duration' => $duration, 'description' => $description];
        return $this;
    }

    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        if (count($this->metrics) > 0) {
            $httpResponse->setHeader('Server-Timing', $this->buildHeaderValue());
        }
        $this->innerResponse->send($httpRequest, $httpResponse);
    }

    private function buildHeaderValue(): string
    {
        $parts = [];
        foreach ($this->metrics as $name => $data) {
            $metric = $name;
            if ($data['description'] !== null) {
                $metric .= sprintf(';desc="%s"', addslashes($data['description']));
            }
            if ($data['duration'] !== null) {
                $metric .= sprintf(';dur=%.2f', $data['duration']);
            }
            $parts[] = $metric;
        }
        return implode(', ', $parts);
    }
}
```

### Usage

```php
// In presenter
public function actionExport(): void
{
    $csvResponse = new CSVResponse($data, 'export.csv');

    $response = new ServerTimingResponse($csvResponse);
    $response->addMetric('generate', 145.3, 'CSV generation');
    $response->addMetric('db', 52.1, 'Database queries');

    $this->sendResponse($response);
}
```

### Pros
- Follows decorator pattern - composes with existing responses
- Immutable, per-request metrics
- No global state
- Works with any `Nette\Application\Response`

### Cons
- Cannot measure timing across presenter lifecycle easily
- Requires wrapping each response manually
- Not suitable for automatic breakpoint measuring

---

## Option 3: Trait + Application Event Integration

**Approach:** Provide a trait for presenters that integrates with Nette Application events for automatic timing.

### Structure
```
src/
└── ServerTiming/
    ├── ServerTiming.php         # Core timing service
    ├── ServerTimingTrait.php    # Presenter trait
    └── ServerTimingListener.php # Application event listener
```

### Implementation

```php
<?php declare(strict_types = 1);

namespace Contributte\Application\ServerTiming;

use Nette\Application\Application;
use Nette\Application\IPresenter;

final class ServerTimingListener
{
    private float $requestStart;

    public function __construct(
        private ServerTiming $serverTiming,
    ) {
        $this->requestStart = hrtime(true);
    }

    public function register(Application $application): void
    {
        $application->onStartup[] = function () {
            $this->serverTiming->add('bootstrap', (hrtime(true) - $this->requestStart) / 1e6, 'Bootstrap');
            $this->serverTiming->start('app');
        };

        $application->onPresenter[] = function (Application $app, IPresenter $presenter) {
            $this->serverTiming->stop('app', 'Routing');
            $this->serverTiming->start('presenter');
        };

        $application->onResponse[] = function () {
            $this->serverTiming->stop('presenter', 'Presenter');
        };

        $application->onShutdown[] = function () {
            // Apply header at the end
        };
    }
}

trait ServerTimingTrait
{
    private ?ServerTiming $serverTiming = null;

    public function injectServerTiming(ServerTiming $serverTiming): void
    {
        $this->serverTiming = $serverTiming;
    }

    protected function measureTiming(string $name, callable $callback, ?string $description = null): mixed
    {
        $this->serverTiming?->start($name);
        try {
            return $callback();
        } finally {
            $this->serverTiming?->stop($name, $description);
        }
    }
}
```

### Usage

```php
// In presenter
class ProductPresenter extends BasePresenter
{
    use ServerTimingTrait;

    public function actionDetail(int $id): void
    {
        $product = $this->measureTiming('db', fn() =>
            $this->productRepository->find($id),
            'Load product'
        );

        $this->measureTiming('related', fn() =>
            $this->loadRelatedProducts($product),
            'Related products'
        );
    }
}
```

### DI Configuration (NEON)

```neon
services:
    serverTiming: Contributte\Application\ServerTiming\ServerTiming
    serverTimingListener:
        factory: Contributte\Application\ServerTiming\ServerTimingListener
        setup:
            - register(@application)
```

### Pros
- Automatic lifecycle timing (bootstrap, routing, presenter)
- Convenient `measureTiming()` helper method
- Integrates with Nette Application events
- Trait provides clean presenter integration

### Cons
- More complex setup
- Requires DI configuration
- Trait-based injection pattern

---

## Option 4: Middleware / Interceptor Approach

**Approach:** Create an Application decorator that wraps the entire request and automatically measures phases.

### Structure
```
src/
└── ServerTiming/
    ├── ServerTiming.php
    ├── ServerTimingMiddleware.php  # Application wrapper
    └── ServerTimingExtension.php   # DI extension for easy setup
```

### Implementation

```php
<?php declare(strict_types = 1);

namespace Contributte\Application\ServerTiming;

use Nette\Application\Application;
use Nette\Application\IPresenter;
use Nette\Http\IResponse;

final class ServerTimingMiddleware
{
    private float $startTime;

    public function __construct(
        private IResponse $httpResponse,
        private bool $enabled = true,
    ) {
        $this->startTime = hrtime(true);
    }

    public function wrap(Application $application): void
    {
        if (!$this->enabled) {
            return;
        }

        $timing = new ServerTiming();
        $timing->add('queue', (hrtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? $this->startTime) * 1e9) / 1e6, 'Queue time');

        $application->onStartup[] = function () use ($timing) {
            $timing->start('routing');
        };

        $application->onPresenter[] = function (Application $app, IPresenter $presenter) use ($timing) {
            $timing->stop('routing', 'Routing');
            $timing->start('action');

            // Inject timing service into presenter if it accepts it
            if (method_exists($presenter, 'setServerTiming')) {
                $presenter->setServerTiming($timing);
            }
        };

        $application->onResponse[] = function () use ($timing) {
            $timing->stop('action', 'Action');
            $timing->add('total', (hrtime(true) - $this->startTime) / 1e6, 'Total');
        };

        $application->onShutdown[] = function () use ($timing) {
            $timing->apply($this->httpResponse);
        };
    }
}

// DI Extension
final class ServerTimingExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'enabled' => Expect::bool(true),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $builder->addDefinition($this->prefix('timing'))
            ->setFactory(ServerTiming::class);

        $builder->addDefinition($this->prefix('middleware'))
            ->setFactory(ServerTimingMiddleware::class, [$config->enabled])
            ->addSetup('wrap', ['@application']);
    }
}
```

### DI Configuration (NEON)

```neon
extensions:
    serverTiming: Contributte\Application\ServerTiming\ServerTimingExtension

serverTiming:
    enabled: %debugMode%  # Only in debug mode
```

### Pros
- Zero-configuration automatic timing
- Measures entire request lifecycle
- DI extension for easy setup
- Can be enabled/disabled via config
- Non-invasive - works without modifying presenters

### Cons
- Less granular control over custom metrics
- Requires DI extension
- More "magical" behavior

---

## Option 5: Attribute-Based Timing (PHP 8+)

**Approach:** Use PHP 8 attributes to mark methods for automatic timing measurement.

### Structure
```
src/
└── ServerTiming/
    ├── ServerTiming.php
    ├── Attribute/
    │   └── Measure.php           # Attribute definition
    ├── MeasuredPresenter.php     # Base presenter with attribute support
    └── ServerTimingExtension.php
```

### Implementation

```php
<?php declare(strict_types = 1);

namespace Contributte\Application\ServerTiming\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Measure
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
    ) {}
}

// Base presenter that processes attributes
abstract class MeasuredPresenter extends Presenter
{
    private ServerTiming $serverTiming;

    public function injectServerTiming(ServerTiming $serverTiming): void
    {
        $this->serverTiming = $serverTiming;
    }

    public function __call(string $name, array $args): mixed
    {
        $method = new ReflectionMethod($this, $name);
        $attributes = $method->getAttributes(Measure::class);

        if (count($attributes) > 0) {
            $measure = $attributes[0]->newInstance();
            $metricName = $measure->name ?? $name;

            $this->serverTiming->start($metricName);
            try {
                return parent::__call($name, $args);
            } finally {
                $this->serverTiming->stop($metricName, $measure->description);
            }
        }

        return parent::__call($name, $args);
    }

    protected function afterRender(): void
    {
        parent::afterRender();
        $this->serverTiming->apply($this->getHttpResponse());
    }
}
```

### Usage

```php
class ProductPresenter extends MeasuredPresenter
{
    #[Measure(name: 'db', description: 'Database query')]
    public function actionDetail(int $id): void
    {
        $this->template->product = $this->repository->find($id);
    }

    #[Measure(description: 'Render products list')]
    public function renderDefault(): void
    {
        $this->template->products = $this->repository->findAll();
    }
}
```

### Pros
- Clean, declarative syntax using modern PHP 8 attributes
- Self-documenting code
- Minimal boilerplate
- IDE support for attributes

### Cons
- Requires PHP 8.0+
- Works only on presenter methods
- Limited to method-level granularity
- More complex implementation (reflection)

---

## Comparison Matrix

| Feature | Option 1 | Option 2 | Option 3 | Option 4 | Option 5 |
|---------|----------|----------|----------|----------|----------|
| **Approach** | Service | Decorator | Trait+Events | Middleware | Attributes |
| **Complexity** | Low | Low | Medium | Medium | High |
| **Auto lifecycle timing** | No | No | Yes | Yes | Partial |
| **Custom metrics** | Yes | Yes | Yes | Limited | Limited |
| **DI Extension needed** | No | No | Optional | Yes | Yes |
| **Framework coupling** | Low | Low | Medium | High | High |
| **Presenter changes** | Manual call | Wrap response | Use trait | None | Extend base |
| **PHP version** | 8.2+ | 8.2+ | 8.2+ | 8.2+ | 8.2+ |
| **Testability** | Excellent | Excellent | Good | Good | Medium |

---

## Recommendation

**For contributte/application**, I recommend **Option 1 (Standalone Service)** combined with **Option 3 (Trait)** as the primary implementation:

1. **Core:** `ServerTiming` service class (Option 1) - simple, testable, no magic
2. **Convenience:** `ServerTimingTrait` for presenters (from Option 3) - easy adoption
3. **Optional:** `ServerTimingExtension` DI extension for zero-config lifecycle timing

This approach:
- Keeps the library lightweight and focused
- Provides flexibility for advanced users
- Offers convenience for common use cases
- Follows existing contributte patterns (see `StructuredTemplates` trait)

---

## References

- [W3C Server Timing Specification](https://www.w3.org/TR/server-timing/)
- [MDN: Server-Timing Header](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Server-Timing)
- [MDN: PerformanceServerTiming API](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceServerTiming)
