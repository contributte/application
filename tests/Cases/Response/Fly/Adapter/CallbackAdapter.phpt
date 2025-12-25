<?php declare(strict_types = 1);

use Contributte\Application\Response\Fly\Adapter\CallbackAdapter;
use Contributte\Tester\Toolkit;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';

Toolkit::test(function (): void {
	$state = new stdClass();
	$state->receivedRequest = null;
	$state->receivedResponse = null;

	$adapter = new CallbackAdapter(function (IRequest $request, IResponse $response) use ($state): void {
		$state->receivedRequest = $request;
		$state->receivedResponse = $response;
	});

	$request = new Request(new UrlScript());
	$response = new Response();

	$adapter->send($request, $response);

	Assert::same($request, $state->receivedRequest);
	Assert::same($response, $state->receivedResponse);
});

Toolkit::test(function (): void {
	$state = new stdClass();
	$state->output = '';

	$adapter = new CallbackAdapter(function () use ($state): void {
		$state->output = 'executed';
	});

	$adapter->send(new Request(new UrlScript()), new Response());

	Assert::same('executed', $state->output);
});
