<?php declare(strict_types = 1);

namespace Contributte\Application\UI;

use Nette\Application\UI\Control;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class NullControl extends Control
{

	/**
	 * Render nothing
	 *
	 * @return void
	 */
	public function render(): void
	{
		// Nothing..
	}

}
