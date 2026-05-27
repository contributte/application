<?php declare(strict_types = 1);

use Contributte\Application\Response\Fly\Adapter\ProcessAdapter;
use Contributte\Tester\Toolkit;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';

// Test send executes command and outputs result
Toolkit::test(function (): void {
	$adapter = new ProcessAdapter('echo "hello world"');

	ob_start();
	$adapter->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same("hello world\n", $output);
});

// Test send with custom buffer size
Toolkit::test(function (): void {
	$adapter = new ProcessAdapter('echo "buffered"', 'r', 4);

	ob_start();
	$adapter->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same("buffered\n", $output);
});

// Test send with multi-line output
Toolkit::test(function (): void {
	$adapter = new ProcessAdapter('printf "line1\nline2\nline3"');

	ob_start();
	$adapter->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same("line1\nline2\nline3", $output);
});
