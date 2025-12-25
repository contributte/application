<?php declare(strict_types = 1);

use Contributte\Application\Response\Fly\Adapter\CallbackAdapter;
use Contributte\Application\Response\Fly\FlyResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

Toolkit::test(function (): void {
	$state = new stdClass();
	$state->called = false;

	$adapter = new CallbackAdapter(function (IRequest $request, IResponse $response) use ($state): void {
		$state->called = true;
		echo 'callback output';
	});

	$flyResponse = new FlyResponse($adapter);

	ob_start();
	$flyResponse->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::true($state->called);
	Assert::same('callback output', $output);
});
