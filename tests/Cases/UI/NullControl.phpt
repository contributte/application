<?php declare(strict_types = 1);

use Contributte\Application\UI\NullControl;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$control = new NullControl();

	ob_start();
	$control->render();
	$output = ob_get_clean();

	Assert::same('', $output);
});
