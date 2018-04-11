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
	 *
	 * @return string
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
	 * @return bool
	 */
	public function isModuleCurrent(string $module): bool
	{
		return strpos($this->getAction(true), $module) !== false;
	}

}
