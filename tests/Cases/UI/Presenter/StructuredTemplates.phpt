<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\UI\StructuredTemplatesPresenter;

require_once __DIR__ . '/../../../bootstrap.php';

$fixturesDir = realpath(__DIR__ . '/../../../Fixtures/UI');

// Template files by view
Toolkit::test(function () use ($fixturesDir): void {
	$presenter = new StructuredTemplatesPresenter();
	$presenter->setView('detail');

	Assert::same([$fixturesDir . '/templates/detail.latte'], $presenter->formatTemplateFiles());
});

// Layout files by presenter directory
Toolkit::test(function () use ($fixturesDir): void {
	$presenter = new StructuredTemplatesPresenter();

	Assert::same([$fixturesDir . '/templates/@layout.latte'], $presenter->formatLayoutTemplateFiles());
});

// Layout disabled
Toolkit::test(function () use ($fixturesDir): void {
	$presenter = new StructuredTemplatesPresenter();
	$presenter->setLayout(false);

	Assert::same([$fixturesDir . '/templates/@layout.latte'], $presenter->formatLayoutTemplateFiles());
});

// Layout as explicit path
Toolkit::test(function (): void {
	$presenter = new StructuredTemplatesPresenter();
	$presenter->setLayout('/path/to/@layout.latte');

	Assert::same(['/path/to/@layout.latte'], $presenter->formatLayoutTemplateFiles());
});
