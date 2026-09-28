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

Contributte Application is a set of responses and presenter helpers for Nette Framework. It sends CSV, JSON, XML,
image, string and PSR-7 stream responses, streams large output on the fly and loads presenter templates from the
presenter's own folder.

## Usage

To install the latest version of `contributte/application`, use [Composer](https://getcomposer.org):

```bash
composer require contributte/application
```

Requires PHP 8.2 or later and Nette 3.2 or later.

Create a response in a presenter and send it:

```php
use Contributte\Application\Response\CSVResponse;
use Nette\Application\UI\Presenter;

final class ExportPresenter extends Presenter
{

	public function actionDefault(): void
	{
		$data = [
			['name', 'email'],
			['John', 'john@example.com'],
		];

		// Downloads users.csv with rows separated by ";"
		$this->sendResponse(new CSVResponse($data, 'users.csv'));
	}

}
```

## Documentation

For details on how to use this package, check out the [documentation](.docs).

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.7`  | `master` | 3.2+  | `>=8.2` |
| stable      | `^0.6`  | `master` | 3.0+  | `>=8.1` |

## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>
<a href="https://github.com/paveljanda">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/1488874?v=3&s=80">
</a>

-----

Consider [supporting](https://contributte.org/partners.html) the **contributte** development team.
Thank you for using this package.
