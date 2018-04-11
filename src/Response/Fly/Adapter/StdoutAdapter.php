<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Adapter;

use Contributte\Application\Response\Fly\Buffer\FileBuffer;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class StdoutAdapter implements Adapter
{

	/** @var callable */
	private $callback;

	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function send(IRequest $request, IResponse $response): void
	{
		// Open file pointer
		$b = new FileBuffer('php://output', 'w');

		// Fire callback with Buffer, request and response
		call_user_func_array($this->callback, [$b, $request, $response]);

		// Close resource
		$b->close();
	}

}
