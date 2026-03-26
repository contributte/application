<?php declare(strict_types = 1);

use Contributte\Application\UI\NullComponent;
use Contributte\Tester\Toolkit;
use Nette\Application\UI\Component;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test NullComponent can be instantiated
Toolkit::test(function (): void {
	$component = new NullComponent();

	Assert::type(NullComponent::class, $component);
});

// Test NullComponent is instance of Nette Component
Toolkit::test(function (): void {
	$component = new NullComponent();

	Assert::type(Component::class, $component);
});
