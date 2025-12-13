<?php declare(strict_types = 1);

namespace Tests\Fixtures\UI;

use Contributte\Application\UI\MagicComponents;
use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;

class TestControlWithTrait extends Control
{

	use MagicComponents;

	protected function createComponent(string $name): ?IComponent
	{
		return $this->tryCreateMagicComponent($name) ?? parent::createComponent($name);
	}

}
