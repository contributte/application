<?php declare(strict_types = 1);

namespace Contributte\Application\UI\Presenter;

use Nette\Application\UI\Presenter;
use ReflectionClass;

/**
 * Use in Presenter
 *
 * @mixin Presenter
 */
trait StructuredTemplates
{

	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles(): array
	{
		$layout = (string) $this->layout;

		if (preg_match('#/|\\\\#', $layout) === 1) {
			return [$layout];
		}

		$called = static::class;
		$classes = [$called] + class_parents($called);
		$list = [];

		foreach ($classes as $class) {
			// Skip Nette classes
			if (str_starts_with($class, 'Nette\\')) {
				continue;
			}

			$presenterReflection = new ReflectionClass($class);
			$fileName = $presenterReflection->getFileName();

			if ($fileName === false) {
				continue;
			}

			$presenterDir = dirname($fileName);
			$list[] = $presenterDir . '/templates/@layout.latte';
		}

		$list = array_unique($list);

		return $list;
	}

	/**
	 * @return string[]
	 */
	public function formatTemplateFiles(): array
	{
		$presenterReflection = new ReflectionClass(static::class);
		$fileName = $presenterReflection->getFileName();

		if ($fileName === false) {
			return [];
		}

		$presenterDir = dirname($fileName);

		return [
			$presenterDir . '/templates/' . $this->view . '.latte',
		];
	}

}
