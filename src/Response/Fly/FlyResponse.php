<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly;

use Contributte\Application\Response\Fly\Adapter\Adapter;
use Nette\Application\IResponse as ApplicationResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class FlyResponse implements ApplicationResponse
{

	/** @var Adapter */
	protected $adapter;

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	public function send(IRequest $request, IResponse $response): void
	{
		$this->adapter->send($request, $response);
	}

}
