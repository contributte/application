<?php

namespace Contributte\Application\Response\Fly\Adapter;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

class CallbackAdapter implements Adapter
{

	/** @var callable */
	private $callback;

	/**
	 * @param callable $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @param IRequest $request
	 * @param IResponse $response
	 * @return void
	 */
	public function send(IRequest $request, IResponse $response)
	{
		call_user_func_array($this->callback, [$request, $response]);
	}

}
