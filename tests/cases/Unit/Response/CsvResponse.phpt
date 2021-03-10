<?php declare(strict_types = 1);

/**
 * Test: Response\CSVResponse
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Contributte\Application\Response\CSVResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

test(static function (): void {
	$csv = new CSVResponse([
		['a', 'b'],
		['c', 'd'],
	], 'some.csv', 'utf-16');
	ob_start();
	$csv->send(new Request(new UrlScript()), new Response());
	$csvOutput = ob_get_clean();

	Assert::equal($csvOutput, mb_convert_encoding("a;b\nc;d\n", 'utf-16'));
});

test(static function (): void {
	$csv = new CSVResponse([
		['a', 'b'],
		['c', 'd'],
	], 'some.csv');
	ob_start();
	$csv->send(new Request(new UrlScript()), new Response());
	$csvOutput = ob_get_clean();

	Assert::equal($csvOutput, "a;b\nc;d\n");
});

test(static function (): void {
	$csv = new CSVResponse([
		['a', 'b'],
		['c', 'd'],
	], 'some.csv', 'windows-1250');
	ob_start();
	$csv->send(new Request(new UrlScript()), new Response());
	$csvOutput = ob_get_clean();

	Assert::equal($csvOutput, iconv('utf-8', 'windows-1250', "a;b\nc;d\n"));
});
