<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters\Front;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;

/**
 * Fixture: Front:Homepage presenter that throws an error
 */
class HomepagePresenter implements IPresenter
{

	public function run(Request $request): Response
	{
		$action = $request->getParameter('action') ?? 'default';

		match ($action) {
			'default' => throw new \RuntimeException('Homepage error occurred'),
			'about' => throw new \RuntimeException('About page error'),
			default => throw new \RuntimeException('Unknown action: ' . $action),
		};
	}

}
