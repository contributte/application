<?php declare(strict_types = 1);

use Contributte\Application\UI\MagicControl;
use Contributte\Tester\Toolkit;
use Nette\Application\UI\Control;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Add and retrieve factories
Toolkit::test(function (): void {
	$magic = new MagicControl();

	Assert::same([], $magic->getFactoryNames());
	Assert::false($magic->hasFactory('test'));

	$factory = fn () => new Control();
	$magic->addFactory('test', $factory);

	Assert::same(['test'], $magic->getFactoryNames());
	Assert::true($magic->hasFactory('test'));
	Assert::same(['test' => $factory], $magic->getFactories());
});

// Test: Set factories in bulk
Toolkit::test(function (): void {
	$magic = new MagicControl();

	$factory1 = fn () => new Control();
	$factory2 = fn () => new Control();

	$magic->setFactories([
		'one' => $factory1,
		'two' => $factory2,
	]);

	Assert::same(['one', 'two'], $magic->getFactoryNames());
	Assert::true($magic->hasFactory('one'));
	Assert::true($magic->hasFactory('two'));
	Assert::false($magic->hasFactory('three'));
});

// Test: Prefix constant
Toolkit::test(function (): void {
	Assert::same('magic-', MagicControl::PREFIX);
});
