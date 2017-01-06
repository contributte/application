<?php

namespace Contributte\Application\DI;

use Contributte\Application\MemoryLinkGenerator;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class LinkGeneratorExtension extends CompilerExtension
{

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('generator'))
			->setClass(MemoryLinkGenerator::class, [
				1 => new Statement('@Nette\Http\IRequest::getUrl'),
			]);
	}

}
