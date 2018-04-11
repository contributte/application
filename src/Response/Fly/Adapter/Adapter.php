<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Adapter;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

interface Adapter
{

	public function send(IRequest $request, IResponse $response): void;

}
