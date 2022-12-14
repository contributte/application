<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

use RuntimeException;

class ProcessBuffer implements Buffer
{

	/** @var resource */
	private $pointer;

	public function __construct(string $command, string $mode = 'r')
	{
		$this->pointer = popen($command, $mode);
	}

	/**
	 * Close and clean
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * @param mixed $data
	 */
	public function write($data): void
	{
		throw new RuntimeException('Not implemented...');
	}

	/**
	 * @return mixed
	 */
	public function read(int $size)
	{
		/** @var positive-int $size */
		return fread($this->pointer, $size);
	}

	public function eof(): bool
	{
		return feof($this->pointer);
	}

	public function close(): int
	{
		if (isset($this->pointer) && is_resource($this->pointer)) {
			$res = fclose($this->pointer);
			unset($this->pointer);

			return (int) $res;
		}

		return 0;
	}

}
