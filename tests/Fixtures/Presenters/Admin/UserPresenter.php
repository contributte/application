<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters\Admin;

use Nette\Application\BadRequestException;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;

/**
 * Fixture: Admin:User presenter that throws errors
 */
class UserPresenter implements IPresenter
{

	public function run(Request $request): Response
	{
		$action = $request->getParameter('action') ?? 'default';
		$id = $request->getParameter('id');

		if ($action === 'list') {
			throw new \RuntimeException('User list error');
		}

		if ($action === 'edit') {
			if ($id !== null && $id <= 0) {
				throw new BadRequestException('User not found', 404);
			}

			throw new \RuntimeException('User edit error');
		}

		throw new \RuntimeException('Unknown action: ' . $action);
	}

}
