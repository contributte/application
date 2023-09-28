<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly;

use Contributte\Application\Response\Fly\Adapter\Adapter;
use Nette\Application\Response;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;

class FlyResponse implements Response
{

	protected Adapter $adapter;

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	public function send(HttpRequest $request, HttpResponse $response): void
	{
		$this->adapter->send($request, $response);
	}

}
