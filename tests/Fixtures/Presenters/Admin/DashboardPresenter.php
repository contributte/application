<?php declare(strict_types = 1);

namespace Tests\Fixtures\Presenters\Admin;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;

/**
 * Fixture: Admin:Dashboard presenter that throws an error
 */
class DashboardPresenter implements IPresenter
{

	public function run(Request $request): Response
	{
		throw new \RuntimeException('Admin dashboard error occurred');
	}

}
