<?php declare(strict_types = 1);

use Contributte\Application\Response\StringResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$content = 'Hello World';
	$response = new StringResponse($content, 'test.txt');

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same($content, $output);
});

Toolkit::test(function (): void {
	$content = 'PDF content';
	$response = new StringResponse($content, 'document.pdf', 'application/pdf');

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same($content, $output);
});

Toolkit::test(function (): void {
	$response = new StringResponse('content', 'file.txt');
	$result = $response->setAttachment();

	Assert::same($response, $result);
});

Toolkit::test(function (): void {
	$response = new StringResponse('content', 'file.txt');
	$response->setAttachment(false);

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	ob_get_clean();

	// Test passes if no exception is thrown
	Assert::true(true);
});
