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
		if (stream_get_meta_data($this->pointer)['mode'] !== 'r') { // readonly stream
			fwrite($this->pointer, $data);
		}
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
