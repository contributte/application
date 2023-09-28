<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

use RuntimeException;

class ProcessBuffer implements Buffer
{

	/** @var resource */
	private $pointer;

	public function __construct(string $command, string $mode = 'r')
	{
		$resource = popen($command, $mode);

		if ($resource === false) {
			throw new RuntimeException('Cannot obtain resource');
		}

		$this->pointer = $resource;
	}

	/**
	 * Close and clean
	 */
	public function __destruct()
	{
		$this->close();
	}

	public function write(mixed $data): void
	{
		throw new RuntimeException('Not implemented...');
	}

	/**
	 * @param positive-int $size
	 */
	public function read(int $size): mixed
	{
		return fread($this->pointer, $size);
	}

	public function eof(): bool
	{
		return feof($this->pointer);
	}

	public function close(): int
	{
		// @phpstan-ignore-next-line
		if (isset($this->pointer) && is_resource($this->pointer)) {
			$res = fclose($this->pointer);
			unset($this->pointer);

			return (int) $res;
		}

		return 0;
	}

}
