<?php declare(strict_types = 1);

use Contributte\Application\Response\JsonPrettyResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$payload = ['key' => 'value'];
	$response = new JsonPrettyResponse($payload);

	Assert::same($payload, $response->getPayload());
	Assert::same('application/json', $response->getContentType());
	Assert::same(200, $response->getCode());
	Assert::null($response->getExpiration());
});

Toolkit::test(function (): void {
	$payload = ['key' => 'value'];
	$response = new JsonPrettyResponse($payload, 'application/json; charset=utf-8');

	Assert::same('application/json; charset=utf-8', $response->getContentType());
});

Toolkit::test(function (): void {
	$response = new JsonPrettyResponse(['test' => true]);

	$response->setCode(201);
	Assert::same(201, $response->getCode());

	$response->setContentType('text/json');
	Assert::same('text/json', $response->getContentType());

	$response->setExpiration('1 hour');
	Assert::same('1 hour', $response->getExpiration());
});

Toolkit::test(function (): void {
	$payload = ['name' => 'test', 'value' => 123];
	$response = new JsonPrettyResponse($payload);

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::contains('"name": "test"', $output);
	Assert::contains('"value": 123', $output);
});
