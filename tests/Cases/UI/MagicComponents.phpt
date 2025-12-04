<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\UI\TestControlWithTrait;
use Tests\Fixtures\UI\TestSubControl;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Add and retrieve factories
Toolkit::test(function (): void {
	$control = new TestControlWithTrait();

	Assert::same([], $control->getMagicComponentFactoryNames());
	Assert::false($control->hasMagicComponentFactory('test'));

	$factory = fn () => new TestSubControl();
	$control->addMagicComponentFactory('test', $factory);

	Assert::same(['test'], $control->getMagicComponentFactoryNames());
	Assert::true($control->hasMagicComponentFactory('test'));
});

// Test: Set factories in bulk
Toolkit::test(function (): void {
	$control = new TestControlWithTrait();

	$control->setMagicComponentFactories([
		'one' => fn () => new TestSubControl(),
		'two' => fn () => new TestSubControl(),
	]);

	Assert::same(['one', 'two'], $control->getMagicComponentFactoryNames());
	Assert::true($control->hasMagicComponentFactory('one'));
	Assert::true($control->hasMagicComponentFactory('two'));
	Assert::false($control->hasMagicComponentFactory('three'));
});

// Test: MagicPrefix constant
Toolkit::test(function (): void {
	$control = new TestControlWithTrait();
	Assert::same('magic-', $control::MAGIC_PREFIX);
});
