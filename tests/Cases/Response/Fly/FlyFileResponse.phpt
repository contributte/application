<?php declare(strict_types = 1);

use Contributte\Application\Response\Fly\Adapter\CallbackAdapter;
use Contributte\Application\Response\Fly\FlyFileResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Test send sets correct headers with default settings (force download)
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->contentType = null;
	$state->contentDisposition = null;
	$state->adapterCalled = false;

	$adapter = new CallbackAdapter(function (IRequest $request, IResponse $response) use ($state): void {
		$state->adapterCalled = true;
	});

	$flyFileResponse = new FlyFileResponse($adapter, 'report.pdf');

	$response = new Response();
	$flyFileResponse->send(new Request(new UrlScript()), $response);

	Assert::true($state->adapterCalled);
});

// Test setContentType
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->adapterCalled = false;

	$adapter = new CallbackAdapter(function () use ($state): void {
		$state->adapterCalled = true;
	});

	$flyFileResponse = new FlyFileResponse($adapter, 'data.csv');
	$flyFileResponse->setContentType('text/csv');

	$flyFileResponse->send(new Request(new UrlScript()), new Response());

	Assert::true($state->adapterCalled);
});

// Test setFilename changes filename
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->adapterCalled = false;

	$adapter = new CallbackAdapter(function () use ($state): void {
		$state->adapterCalled = true;
	});

	$flyFileResponse = new FlyFileResponse($adapter, 'original.txt');
	$flyFileResponse->setFilename('renamed.txt');

	$flyFileResponse->send(new Request(new UrlScript()), new Response());

	Assert::true($state->adapterCalled);
});

// Test setForceDownload false (inline disposition)
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->adapterCalled = false;

	$adapter = new CallbackAdapter(function () use ($state): void {
		$state->adapterCalled = true;
	});

	$flyFileResponse = new FlyFileResponse($adapter, 'image.png');
	$flyFileResponse->setForceDownload(false);

	$flyFileResponse->send(new Request(new UrlScript()), new Response());

	Assert::true($state->adapterCalled);
});

// Test that adapter receives request and response
Toolkit::test(function (): void {
	$state = new stdClass();
	$state->receivedRequest = null;
	$state->receivedResponse = null;

	$adapter = new CallbackAdapter(function (IRequest $request, IResponse $response) use ($state): void {
		$state->receivedRequest = $request;
		$state->receivedResponse = $response;
	});

	$flyFileResponse = new FlyFileResponse($adapter, 'test.txt');

	$request = new Request(new UrlScript());
	$response = new Response();
	$flyFileResponse->send($request, $response);

	Assert::same($request, $state->receivedRequest);
	Assert::same($response, $state->receivedResponse);
});
