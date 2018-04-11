<?php declare(strict_types = 1);

/**
 * Test: DI\LinkGeneratorExtension
 */

use Contributte\Application\DI\LinkGeneratorExtension;
use Contributte\Application\ILinkGenerator;
use Contributte\Application\MemoryLinkGenerator;
use Nette\Bridges\ApplicationDI\ApplicationExtension;
use Nette\Bridges\ApplicationDI\RoutingExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('link', new LinkGeneratorExtension());
		$compiler->addExtension('application', new ApplicationExtension());
		$compiler->addExtension('routing', new RoutingExtension());
		$compiler->addExtension('http', new HttpExtension());
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(MemoryLinkGenerator::class, $container->getService('link.generator'));
	Assert::type(MemoryLinkGenerator::class, $container->getByType(ILinkGenerator::class));
});
