<?php

namespace Contributte\Application\UI\Presenter;

use Nette\Application\UI\ComponentReflection;

/**
 * Use in Presenter
 */
trait StructuredTemplates
{

	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles()
	{
		$presenterReflection = new ComponentReflection(get_called_class());
		$presenterDir = dirname($presenterReflection->getFileName());

		$parentPresenterReflection = new ComponentReflection(self::class);
		$parentPresenterDir = dirname($parentPresenterReflection->getFileName());

		return [
			$presenterDir . '/templates/@layout.latte',
			$parentPresenterDir . '/templates/@layout.latte',
		];
	}

	/**
	 * @return string[]
	 */
	public function formatTemplateFiles()
	{
		$presenterReflection = new ComponentReflection(get_called_class());
		$presenterDir = dirname($presenterReflection->getFileName());

		return [
			$presenterDir . '/templates/' . $this->view . '.latte',
		];
	}

}
