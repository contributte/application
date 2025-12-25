<?php declare(strict_types = 1);

use Contributte\Application\Response\XmlResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$xml = '<root><item>test</item></root>';
	$response = new XmlResponse($xml);

	Assert::same($xml, $response->getSource());
});

Toolkit::test(function (): void {
	$xml = '<root><item>test</item></root>';
	$response = new XmlResponse($xml);

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same($xml, $output);
});
