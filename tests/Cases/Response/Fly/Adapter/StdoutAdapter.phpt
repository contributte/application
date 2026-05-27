<?php declare(strict_types = 1);

use Contributte\Application\Response\Fly\Adapter\StdoutAdapter;
use Contributte\Application\Response\Fly\Buffer\Buffer;
use Contributte\Tester\Toolkit;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';

// Test callback receives buffer, request, and response
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->receivedBuffer = null;
	$state->receivedRequest = null;
	$state->receivedResponse = null;

	$adapter = new StdoutAdapter(function (Buffer $buffer, IRequest $request, IResponse $response) use ($state): void {
		$state->receivedBuffer = $buffer;
		$state->receivedRequest = $request;
		$state->receivedResponse = $response;
	});

	$request = new Request(new UrlScript());
	$response = new Response();

	$adapter->send($request, $response);

	Assert::type(Buffer::class, $state->receivedBuffer);
	Assert::same($request, $state->receivedRequest);
	Assert::same($response, $state->receivedResponse);
});

// Test writing to buffer produces output
Toolkit::test(function (): void {
	$adapter = new StdoutAdapter(function (Buffer $buffer): void {
		$buffer->write('hello from stdout');
	});

	ob_start();
	$adapter->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same('hello from stdout', $output);
});

// Test callback without arguments
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->executed = false;

	$adapter = new StdoutAdapter(function () use ($state): void {
		$state->executed = true;
	});

	$adapter->send(new Request(new UrlScript()), new Response());

	Assert::true($state->executed);
});
