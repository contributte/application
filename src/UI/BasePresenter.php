<?php declare(strict_types = 1);

namespace Contributte\Application\UI;

use Nette\Application\UI\Presenter;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
abstract class BasePresenter extends Presenter
{

	/**
	 * Gets module name
	 */
	public function getModuleName(): string
	{
		$parts = explode(':', $this->getName());

		return current($parts);
	}

	/**
	 * Is current module active?
	 *
	 * @param string $module Module name
	 */
	public function isModuleCurrent(string $module): bool
	{
		return strpos($this->getAction(true), $module) !== false;
	}

}
