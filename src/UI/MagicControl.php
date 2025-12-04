<?php declare(strict_types = 1);

namespace Contributte\Application\UI;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;

/**
 * MagicControl serves as a registry and factory for dynamic component creation.
 *
 * Can be used in two ways:
 *
 * 1. Direct usage via MagicComponentTrait in your presenter:
 *    - Add the trait to your presenter
 *    - Inject factories via injectMagicComponents()
 *    - Override createComponent() to call tryCreateMagicComponent()
 *
 * 2. As a standalone control:
 *    - Add MagicControl to your presenter as 'magic' component
 *    - Access subcomponents via {control magic-latestArticles}
 *
 * Neon configuration example:
 *   application:
 *     components:
 *       latestArticles: App\LatestArticlesControlFactory
 *
 * Latte usage:
 *   {control magic-latestArticles}
 *   {control magic-latestArticles, count: 10}
 */
class MagicControl extends Control
{

	public const PREFIX = 'magic-';

	/** @var array<string, callable> */
	private array $factories = [];

	/**
	 * Register a component factory
	 */
	public function addFactory(string $name, callable $factory): void
	{
		$this->factories[$name] = $factory;
	}

	/**
	 * Set factories in bulk
	 *
	 * @param array<string, callable> $factories
	 */
	public function setFactories(array $factories): void
	{
		$this->factories = $factories;
	}

	/**
	 * Get all registered factories
	 *
	 * @return array<string, callable>
	 */
	public function getFactories(): array
	{
		return $this->factories;
	}

	/**
	 * Get all registered factory names
	 *
	 * @return string[]
	 */
	public function getFactoryNames(): array
	{
		return array_keys($this->factories);
	}

	/**
	 * Check if a factory is registered
	 */
	public function hasFactory(string $name): bool
	{
		return isset($this->factories[$name]);
	}

	/**
	 * Create component by name
	 * Handles both direct names and magic-prefixed names
	 */
	protected function createComponent(string $name): ?IComponent
	{
		// First try parent's createComponent (for explicitly defined components)
		$component = parent::createComponent($name);
		if ($component !== null) {
			return $component;
		}

		// Try direct factory lookup first
		if (isset($this->factories[$name])) {
			return ($this->factories[$name])();
		}

		// Check if this is a magic-prefixed component request
		if (str_starts_with($name, self::PREFIX)) {
			$componentName = substr($name, strlen(self::PREFIX));

			if (isset($this->factories[$componentName])) {
				return ($this->factories[$componentName])();
			}
		}

		return null;
	}

}
