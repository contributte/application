<?php declare(strict_types = 1);

namespace Contributte\Application\DI;

use Contributte\Application\MemoryLinkGenerator;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class LinkGeneratorExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('generator'))
			->setClass(MemoryLinkGenerator::class, [
				1 => new Statement('@Nette\Http\IRequest::getUrl'),
			]);
	}

}
