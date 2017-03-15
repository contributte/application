<?php

namespace Contributte\Application\Response\Fly\Buffer;

class FileBuffer implements Buffer
{

	/** @var resource */
	private $pointer;

	/**
	 * @param string $file
	 * @param string $mode
	 */
	public function __construct($file, $mode)
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
	 * @return void
	 */
	public function write($data)
	{
		fwrite($this->pointer, $data);
	}

	/**
	 * @param int $size
	 * @return mixed
	 */
	public function read($size)
	{
		return fread($this->pointer, $size);
	}

	/**
	 * @return bool
	 */
	public function eof()
	{
		return feof($this->pointer);
	}

	/**
	 * @return bool
	 */
	public function close()
	{
		if (isset($this->pointer) && $this->pointer) {
			$res = fclose($this->pointer);
			unset($this->pointer);

			return $res;
		}

		return 0;
	}

}
