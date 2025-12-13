<?php declare(strict_types = 1);

namespace Tests\Fixtures\UI;

use Nette\Application\UI\Control;

class TestSubControl extends Control
{

	public string $value = 'default';

	public function render(): void
	{
		echo $this->value;
	}

}
