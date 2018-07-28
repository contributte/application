# Application

## Content

- [LinkGenerator (LinkGeneratorExtesion)](#link-generator)
- [UI](#ui)
    - [Presenter](#presenter)
        - [StructuredTemplates](#structuredtemplates)
    - [Control](#control)
    - [Component](#component)
- [Responses](#responses)
    - [CSVResponse](#csvresponse)
    - [ImageResponse](#imageresponse)
    - [JsonPrettyResponse](#psr7streamresponse)
    - [PSR7StreamResponse](#flyresponse)
    - [FlyResponse - send file/buffer on-the-fly](#flyresponse)
    - [XmlResponse](#xmlresponse)

## Link Generator

There is a tiny extension which provides cachable LinkGenerator. LinkGenerator is a external service for creating 
URL addreses / links out of presenter scope. For example in mail templates.

```yaml
extensions:
    link: Contributte\Application\DI\LinkGeneratorExtension
```

## UI

### Presenter

Extending `BasePresenter` you can use these methods:

| Methods | Return | Description |
|---------|--------|-------------|
| `isModuleCurrent($module)` | `boolean` | Is current presenter in given module? |
| `getModuleName()` | `string` | Get current presenter module's name. |

#### Structured Templates

Trait which modifies where are presenter templates located.

- Views
    - `%presenterDir%/templates/%view%.latte`
- Layouts
    - `%presenterDir%/templates/@layout.latte`
    - layouts of parent presenters are also looked for

```php
use Contributte\Application\UI\Presenter\StructuredTemplates;
use Nette\Application\UI\Presenter;

class YourPresenter extends Presenter
{
    use StructuredTemplates;
}
```

### Control

- NullControl - displays nothing

### Component

- NullComponent - displays nothing

## Responses

- CSVResponse
- ImageResponse
- JsonPrettyResponse
- PSR7StreamResponse
- FlyResponse
- XmlResponse

### CSVResponse

```php
$presenter->sendResponse(new CSVResponse($data));

# Define own filename
$presenter->sendResponse(new CSVResponse($data, 'export-2018.csv');

# Set delimiter and include BOM
$presenter->sendResponse(new CSVResponse($data, 'export.csv', 'utf-8', '|', TRUE));
```
### ImageResponse

```php
$presenter->sendResponse(new ImageResponse($image));

# String filepath
$presenter->sendResponse(new ImageResponse('/path/to/file.png'));
```

### JsonPrettyResponse

```php
$presenter->sendResponse(new JsonPrettyResponse($json, 'application/json));
```

### PSR7StreamResponse

```php
$presenter->sendResponse(new PSR7StreamResponse($stream, 'invoice.pdf', 'application/octet-stream'));
```

### FlyResponse

There are 2 types of fly response:

- **FlyResponse** - For common purpose and your custom solutions.
- **FlyFileResponse** - Special response for handling files on-the-fly.

### XmlResponse

```php
$presenter->sendResponse(new XmlResponse($xml));
```

### Adapters

#### ProcessAdapter

Execute command over [popen](http://php.net/manual/en/function.popen.php).

```php
use Contributte\Application\Response\Fly\Adapter\ProcessAdapter;
use Contributte\Application\Response\Fly\FlyFileResponse;

// Compress current folder and send to response
$adapter = new ProcessAdapter('tar cf - ./ | gzip -c -f');
$response = new FlyFileResponse($adapter, 'folder.tgz');

$this->sendResponse($response);
```

#### StdoutAdapter

Write to `php://output`.

```php
use Contributte\Application\Response\Fly\Adapter\StdoutAdapter;
use Contributte\Application\Response\Fly\Buffer\Buffer;
use Contributte\Application\Response\Fly\FlyFileResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

// Write to stdout over buffer class
$adapter = new StdoutAdapter(function(Buffer $buffer, IRequest $request, IResponse $response) {
    // Modify headers
    $response->setHeader(..);
    
    // Write data
    $buffer->write('Some data..');
});
$response = new FlyFileResponse($adapter, 'my.data');

$this->sendResponse($response);
```

#### CallbackAdapter

```php
use Contributte\Application\Response\Fly\Adapter\CallbackAdapter;
use Contributte\Application\Response\Fly\Buffer\Buffer;
use Contributte\Application\Response\Fly\FlyFileResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

$adapter = new CallbackAdapter(function(IRequest $request, IResponse $response) use ($model) {
    // Modify headers
    $response->setHeader(..);
    
    // Fetch topsecret data
    $data = $this->facade->getData();
    foreach ($data as $d) {
        // Write or print data..
    }
});
$response = new FlyFileResponse($adapter, 'my.data');

$this->sendResponse($response);
```

### Model

```php
final class BigOperationHandler
{

    /** @var Facade */
    private $facade;

    /**
     * @param Facade $facade
     */
    public function __construct(Facade $facade)
    {
        $this->facade = $facade;
    }

    public function toFlyResponse()
    {
        $adapter = new CallbackAdapter(function (IRequest $request, IResponse $response) {
            // Modify headers
            $response->setHeader(..);

            // Fetch topsecret data
            $data = $this->facade->getData();
            foreach ($data as $d) {
                // Write or print data..
            }
        });

        return new FlyFileResponse($adapter, 'file.ext');

        // or
        return new FlyResponse($adapter);
    }
}

interface IBigOperationHandlerFactory
{

    /**
     * @return BigOperationHandler
     */
    public function create();

}

final class MyPresenter extends Nette\Application\UI\Presenter
{

    /** @var IBigOperationHandlerFactory @inject */
    public $bigOperationHandlerFactory;

    public function handleMagic()
    {
        $this->sendResponse(
            $this->bigOperationHandlerFactory->create()->toFlyResponse()
        );
    }
}
```
