<?php declare(strict_types = 1);

namespace Contributte\Application\UI\Presenter;

use Nette\Utils\Strings;
use ReflectionClass;

/**
 * Use in Presenter
 */
trait StructuredTemplates
{

	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles(): array
	{
		$called = get_called_class();
		$classes = [$called] + class_parents($called);
		$list = [];

		foreach ($classes as $class) {
			// Skip Nette classes
			if (Strings::startsWith($class, 'Nette\\')) continue;

			$presenterReflection = new ReflectionClass($class);
			$presenterDir = dirname($presenterReflection->getFileName());
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
		$presenterReflection = new ReflectionClass(get_called_class());
		$presenterDir = dirname($presenterReflection->getFileName());

		return [
			$presenterDir . '/templates/' . $this->view . '.latte',
		];
	}

}
