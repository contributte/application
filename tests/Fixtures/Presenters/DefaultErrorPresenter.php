<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\TextResponse;

/**
 * Fixture: Default/fallback error presenter
 */
class DefaultErrorPresenter implements IPresenter
{

	public const PRESENTER_NAME = 'Error';

	private ?Request $request = null;

	public function run(Request $request): Response
	{
		$this->request = $request;

		$exception = $request->getParameter('exception');
		$message = $exception instanceof \Throwable ? $exception->getMessage() : 'Unknown error';

		return new TextResponse('Default Error: ' . $message);
	}

	public function getRequest(): ?Request
	{
		return $this->request;
	}

}
