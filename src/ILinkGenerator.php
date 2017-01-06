<?php

namespace Contributte\Application;

use Nette\Application\UI\InvalidLinkException;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface ILinkGenerator
{

	/**
	 * @param string $dest in format "[[[module:]presenter:]action] [#fragment]"
	 * @param array $params
	 * @return string
	 * @throws InvalidLinkException
	 */
	public function link($dest, array $params = []);

}
