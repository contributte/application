<?php declare(strict_types = 1);

namespace Contributte\Application\UI;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;

/**
 * Trait for enabling magic component creation in presenters and controls.
 *
 * Usage in presenter/control:
 *   use MagicComponents;
 *
 *   public function injectMagicComponents(MagicControl $magicControl): void
 *   {
 *       $this->setMagicComponentFactories($magicControl->getFactories());
 *   }
 *
 *   protected function createComponent(string $name): ?IComponent
 *   {
 *       return $this->tryCreateMagicComponent($name) ?? parent::createComponent($name);
 *   }
 *
 * Usage in Latte:
 *   {control magic-latestArticles}
 *   {control magic-latestArticles, count: 10}
 *
 * @mixin Presenter
 * @mixin Control
 */
trait MagicComponents
{

	public const MAGIC_PREFIX = 'magic-';

	/** @var array<string, callable> */
	private array $magicComponentFactories = [];

	/**
	 * Register a magic component factory
	 */
	public function addMagicComponentFactory(string $name, callable $factory): void
	{
		$this->magicComponentFactories[$name] = $factory;
	}

	/**
	 * Set magic component factories in bulk
	 *
	 * @param array<string, callable> $factories
	 */
	public function setMagicComponentFactories(array $factories): void
	{
		$this->magicComponentFactories = $factories;
	}

	/**
	 * Check if a magic component factory is registered
	 */
	public function hasMagicComponentFactory(string $name): bool
	{
		return isset($this->magicComponentFactories[$name]);
	}

	/**
	 * Get all registered magic component factory names
	 *
	 * @return string[]
	 */
	public function getMagicComponentFactoryNames(): array
	{
		return array_keys($this->magicComponentFactories);
	}

	/**
	 * Try to create a magic component by name.
	 * Returns null if the name doesn't match the magic prefix or if no factory is registered.
	 *
	 * Call this from your createComponent() method:
	 *   return $this->tryCreateMagicComponent($name) ?? parent::createComponent($name);
	 */
	protected function tryCreateMagicComponent(string $name): ?IComponent
	{
		if (!str_starts_with($name, self::MAGIC_PREFIX)) {
			return null;
		}

		$componentName = substr($name, strlen(self::MAGIC_PREFIX));

		if (!isset($this->magicComponentFactories[$componentName])) {
			return null;
		}

		$factory = $this->magicComponentFactories[$componentName];

		return $factory();
	}

	/**
	 * Create a magic component by name with arguments
	 *
	 * @param mixed ...$args Arguments to pass to the factory
	 */
	protected function createMagicComponent(string $name, mixed ...$args): ?IComponent
	{
		if (!isset($this->magicComponentFactories[$name])) {
			return null;
		}

		$factory = $this->magicComponentFactories[$name];

		return $factory(...$args);
	}

}
