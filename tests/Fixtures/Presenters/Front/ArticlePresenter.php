<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters\Front;

use Nette\Application\BadRequestException;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;

/**
 * Fixture: Front:Article presenter that throws errors
 */
class ArticlePresenter implements IPresenter
{

	public function run(Request $request): Response
	{
		$action = $request->getParameter('action') ?? 'default';
		$id = $request->getParameter('id');

		if ($action === 'detail') {
			if ($id !== null && $id <= 0) {
				throw new BadRequestException('Article not found', 404);
			}

			throw new \RuntimeException('Article detail error');
		}

		throw new \RuntimeException('Unknown action: ' . $action);
	}

}
