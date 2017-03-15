<?php

namespace Contributte\Application\Response\Fly;

use Contributte\Application\Response\Fly\Adapter\Adapter;
use Nette\Application\IResponse as ApplicationResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class FlyResponse implements ApplicationResponse
{

	/** @var Adapter */
	protected $adapter;

	/**
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @param IRequest $request
	 * @param IResponse $response
	 * @return void
	 */
	public function send(IRequest $request, IResponse $response)
	{
		$this->adapter->send($request, $response);
	}

}
