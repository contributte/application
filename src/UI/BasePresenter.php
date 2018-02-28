<?php

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
	public function getModuleName()
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
	public function isModuleCurrent($module)
	{
		return strpos($this->getAction(TRUE), $module) !== FALSE;
	}

}
