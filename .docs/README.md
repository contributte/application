# Application

## Content

- [LinkGenerator (LinkGeneratorExtesion)](#link-generator)
- [FlyResponse - send file/buffer on-the-fly](#flyresponse)

## Link Generator

There is a tiny extension which provides cachable LinkGenerator. LinkGenerator is a external service for creating 
URL addreses / links out of presenter scope. For example in mail templates.

```yaml
extensions:
    link: Contributte\Application\DI\LinkGeneratorExtension
```

## FlyResponse

### FlyResponse

For common purpose and your custom solutions.

### FlyFileResponse

Special response for handling files on-the-fly.

## Adapters

### ProcessAdapter

Execute command over [popen](http://php.net/manual/en/function.popen.php).

```php
use Contributte\Application\Response\Fly\Adapter\ProcessAdapter;
use Contributte\Application\Response\Fly\FlyFileResponse;

// Compress current folder and send to response
$adapter = new ProcessAdapter('tar cf - ./ | gzip -c -f');
$response = new FlyFileResponse($adapter, 'folder.tgz');

$this->sendResponse($response);
```

### StdoutAdapter

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

### CallbackAdapter

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

## Model

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
