<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Adapter;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

class CallbackAdapter implements Adapter
{

	/** @var callable */
	private $callback;

	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function send(IRequest $request, IResponse $response): void
	{
		call_user_func_array($this->callback, [$request, $response]);
	}

}
