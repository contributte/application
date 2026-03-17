<?php declare(strict_types = 1);

namespace Contributte\Application\ErrorPresenter;

use Nette\Application\Request;

interface ErrorPresenterLocator
{

	/**
	 * Locates the appropriate error presenter based on the original request.
	 *
	 * @return string|null The error presenter name (e.g., 'Front:Error') or null if no mapping found
	 */
	public function locate(?Request $request): ?string;

}
