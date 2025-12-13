<?php declare(strict_types = 1);

namespace Contributte\Application\DI;

use Contributte\Application\UI\MagicControl;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * Contributte Application DI Extension
 *
 * Example configuration:
 *   application:
 *     components:
 *       latestArticles: App\LatestArticlesControlFactory
 *       sidebar: App\SidebarControlFactory
 */
class ApplicationExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'components' => Expect::arrayOf(
				Expect::string()->required()
			)->default([]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		// Skip if no components configured
		if ($config->components === []) {
			return;
		}

		// Register MagicControl service
		$magicControl = $builder->addDefinition($this->prefix('magicControl'))
			->setFactory(MagicControl::class)
			->setAutowired(true);

		// Add factory references
		foreach ($config->components as $name => $factoryClass) {
			$this->addComponentFactory($magicControl, (string) $name, $factoryClass);
		}
	}

	private function addComponentFactory(ServiceDefinition $magicControl, string $name, string $factoryClass): void
	{
		$builder = $this->getContainerBuilder();

		// Get or register the factory service
		$serviceName = $builder->getByType($factoryClass) ?? $this->registerFactory($factoryClass);

		// Create a factory callback that uses the DI container to get the factory service
		$magicControl->addSetup('addFactory', [
			$name,
			ContainerBuilder::literal('fn() => $this->getService(?)->create()', [$serviceName]),
		]);
	}

	private function registerFactory(string $factoryClass): string
	{
		$builder = $this->getContainerBuilder();
		$serviceName = $this->prefix('factory.' . md5($factoryClass));

		if (!$builder->hasDefinition($serviceName)) {
			$builder->addFactoryDefinition($serviceName)
				->setImplement($factoryClass);
		}

		return $serviceName;
	}

}
