<?php

/**
 * Test: Response\Fly\Buffer\FileBuffer
 */

use Contributte\Application\Response\Fly\Buffer\FileBuffer;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';

test(function () {
	$file = TEMP_DIR . '/test1.file' . time();
	$b = new FileBuffer($file, 'w');
	$b->write('foobar');
	$b->close();

	Assert::equal('foobar', file_get_contents($file));
});

test(function () {
	$file = TEMP_DIR . '/test2.file' . time();
	file_put_contents($file, 'foobar');

	$b = new FileBuffer($file, 'r');
	$b->write('test');

	Assert::equal('foobar', file_get_contents($file));
});

test(function () {
	$file = TEMP_DIR . '/test3.file' . time();
	file_put_contents($file, 'foobar');

	$b = new FileBuffer($file, 'r');

	Assert::equal('foo', $b->read(3));
	Assert::equal('bar', $b->read(3));
	Assert::equal('', $b->read(1));
	Assert::true($b->eof());
});
