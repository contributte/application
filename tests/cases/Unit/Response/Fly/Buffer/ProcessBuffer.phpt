<?php declare(strict_types = 1);

/**
 * Test: Response\Fly\Buffer\ProcessBuffer
 */

use Contributte\Application\Response\Fly\Buffer\ProcessBuffer;
use Tester\Assert;

require_once __DIR__ . '/../../../../../bootstrap.php';

test(function (): void {
	$b = new ProcessBuffer('date');
	$data = $b->read(128);

	Assert::equal(trim(@exec('date')), trim($data));
});
