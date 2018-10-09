<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

class FileBuffer implements Buffer
{

	/** @var resource */
	private $pointer;

	public function __construct(string $file, string $mode)
	{
		$this->pointer = fopen($file, $mode);
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
		fwrite($this->pointer, $data);
	}

	/**
	 * @return mixed
	 */
	public function read(int $size)
	{
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
