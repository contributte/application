<?php declare(strict_types = 1);

namespace Tests\Fixtures\DI;

use Nette\Application\UI\Control;

class TestArticleControl extends Control
{

	public function render(): void
	{
		echo 'Article';
	}

}
