<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters\Admin;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\TextResponse;

/**
 * Fixture: Admin module error presenter
 */
class ErrorPresenter implements IPresenter
{

	public const PRESENTER_NAME = 'Admin:Error';

	private ?Request $request = null;

	public function run(Request $request): Response
	{
		$this->request = $request;

		$exception = $request->getParameter('exception');
		$message = $exception instanceof \Throwable ? $exception->getMessage() : 'Unknown error';

		return new TextResponse('Admin Error: ' . $message);
	}

	public function getRequest(): ?Request
	{
		return $this->request;
	}

}
