<?php declare(strict_types = 1);

use Contributte\Application\Response\PSR7StreamResponse;
use Contributte\Tester\Toolkit;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Psr\Http\Message\StreamInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test default content type
Toolkit::test(function (): void {
	$stream = createStream('test');
	$response = new PSR7StreamResponse($stream, 'file.bin');

	Assert::same('application/octet-stream', $response->getContentType());
});

// Test custom content type
Toolkit::test(function (): void {
	$stream = createStream('test');
	$response = new PSR7StreamResponse($stream, 'file.pdf', 'application/pdf');

	Assert::same('application/pdf', $response->getContentType());
});

// Test getters
Toolkit::test(function (): void {
	$stream = createStream('test');
	$response = new PSR7StreamResponse($stream, 'document.txt', 'text/plain');

	Assert::same($stream, $response->getStream());
	Assert::same('document.txt', $response->getName());
	Assert::same('text/plain', $response->getContentType());
});

// Test send outputs stream content
Toolkit::test(function (): void {
	$stream = createStream('Hello PSR7 World');
	$response = new PSR7StreamResponse($stream, 'hello.txt', 'text/plain');

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same('Hello PSR7 World', $output);
});

// Test send with empty stream
Toolkit::test(function (): void {
	$stream = createStream('');
	$response = new PSR7StreamResponse($stream, 'empty.txt');

	ob_start();
	$response->send(new Request(new UrlScript()), new Response());
	$output = ob_get_clean();

	Assert::same('', $output);
});

function createStream(string $content): StreamInterface
{
	return new class ($content) implements StreamInterface {

		private string $content;

		private int $position = 0;

		public function __construct(string $content)
		{
			$this->content = $content;
		}

		public function __toString(): string
		{
			return $this->content;
		}

		public function close(): void
		{
		}

		public function detach()
		{
			return null;
		}

		public function getSize(): ?int
		{
			return strlen($this->content);
		}

		public function tell(): int
		{
			return $this->position;
		}

		public function eof(): bool
		{
			return $this->position >= strlen($this->content);
		}

		public function isSeekable(): bool
		{
			return true;
		}

		public function seek(int $offset, int $whence = SEEK_SET): void
		{
			$this->position = $offset;
		}

		public function rewind(): void
		{
			$this->position = 0;
		}

		public function isWritable(): bool
		{
			return false;
		}

		public function write(string $string): int
		{
			return 0;
		}

		public function isReadable(): bool
		{
			return true;
		}

		public function read(int $length): string
		{
			$data = substr($this->content, $this->position, $length);
			$this->position += strlen($data);

			return $data;
		}

		public function getContents(): string
		{
			return substr($this->content, $this->position);
		}

		public function getMetadata(?string $key = null)
		{
			return $key === null ? [] : null;
		}

	};
}
