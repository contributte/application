<?php declare(strict_types = 1);

use Contributte\Application\Response\ImageResponse;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Nette\InvalidArgumentException;
use Nette\Utils\Image;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test constructor throws exception for non-existent file
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		new ImageResponse('/non/existent/file.jpg');
	}, InvalidArgumentException::class, 'Image must be Nette\Utils\Image or file path');
});

// Test constructor accepts Nette\Utils\Image instance
Toolkit::test(function (): void {
	$image = Image::fromBlank(1, 1);
	$response = new ImageResponse($image);

	Assert::type(ImageResponse::class, $response);
});

// Test constructor accepts valid file path
Toolkit::test(function (): void {
	$tmpDir = Environment::getTmpDir();
	$filePath = $tmpDir . '/test-image.png';

	$image = Image::fromBlank(1, 1);
	$image->save($filePath);

	$response = new ImageResponse($filePath);

	Assert::type(ImageResponse::class, $response);

	unlink($filePath);
});

// Test constructor with custom type and quality
Toolkit::test(function (): void {
	$image = Image::fromBlank(1, 1);
	$response = new ImageResponse($image, Image::PNG, 9);

	Assert::type(ImageResponse::class, $response);
});
