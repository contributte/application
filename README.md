![](https://heatbadger.now.sh/github/readme/contributte/application/)

<p align=center>
  <a href="https://github.com/contributte/application/actions"><img src="https://badgen.net/github/checks/contributte/application/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/application"><img src="https://badgen.net/codecov/c/github/contributte/application"></a>
  <a href="https://packagist.org/packages/contributte/application"><img src="https://badgen.net/packagist/dm/contributte/application"></a>
  <a href="https://packagist.org/packages/contributte/application"><img src="https://badgen.net/packagist/v/contributte/application"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/application"><img src="https://badgen.net/packagist/php/contributte/application"></a>
  <a href="https://github.com/contributte/application"><img src="https://badgen.net/github/license/contributte/application"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Small extensions for Nette Application, including base UI helpers, null components, structured templates, and response implementations.

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.7`  | `master` | 3.2+  | `>=8.2` |
| stable      | `^0.6`  | `master` | 3.0+  | `>=8.1` |

## Installation

To install latest version of `contributte/application` use [Composer](https://getcomposer.org).

```bash
composer require contributte/application
```

## Content

- [UI](#ui)
  - [Presenter](#presenter)
    - [Structured Templates](#structured-templates)
  - [Control](#control)
  - [Component](#component)
- [Responses](#responses)
  - [CSVResponse](#csvresponse)
  - [ImageResponse](#imageresponse)
  - [JsonPrettyResponse](#jsonprettyresponse)
  - [PSR7StreamResponse](#psr7streamresponse)
  - [FlyResponse](#flyresponse)
  - [XmlResponse](#xmlresponse)
  - [StringResponse](#stringresponse)
  - [Adapters](#adapters)
  - [Model](#model)

## UI

### Presenter

By extending the `BasePresenter` you can use these methods:

| Methods | Return | Description |
|---------|--------|-------------|
| `isModuleCurrent($module)` | `boolean` | Is the current presenter in a given module? |
| `getModuleName()` | `string` | Get current presenter's module name. |

#### Structured Templates

A trait which modifies where the presenter templates are loaded from.

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
- StringResponse

### CSVResponse

```php
$presenter->sendResponse(new CSVResponse($data));

// Define own filename
$presenter->sendResponse(new CSVResponse($data, 'export-2018.csv'));

// Set delimiter and include BOM
$presenter->sendResponse(new CSVResponse($data, 'export.csv', 'utf-8', '|', true));
```

### ImageResponse

```php
$presenter->sendResponse(new ImageResponse($image));

// String filepath
$presenter->sendResponse(new ImageResponse('/path/to/file.png'));
```

### JsonPrettyResponse

```php
$presenter->sendResponse(new JsonPrettyResponse($json, 'application/json'));
```

### PSR7StreamResponse

```php
$presenter->sendResponse(new PSR7StreamResponse($stream, 'invoice.pdf', 'application/octet-stream'));
```

### FlyResponse

There are 2 types of fly response:

- **FlyResponse** - General purpose fly response.
- **FlyFileResponse** - Special response for handling files on-the-fly.

### XmlResponse

```php
$presenter->sendResponse(new XmlResponse($xml));
```

### StringResponse

```php
$response = new StringResponse($pdfString, 'invoice.pdf', 'application/pdf');
$response->setAttachment(); // browser download the file

$presenter->sendResponse($response);
```

### Adapters

#### ProcessAdapter

Execute a command over [popen](https://www.php.net/manual/en/function.popen.php).

```php
use Contributte\Application\Response\Fly\Adapter\ProcessAdapter;
use Contributte\Application\Response\Fly\FlyFileResponse;

// Compress the current folder and send it to a response
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

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>
<a href="https://github.com/paveljanda">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/1488874?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners) **contributte** development team.
Also thank you for using this package.
