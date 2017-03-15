<?php

namespace Contributte\Application\Response\Fly\Buffer;

use Exception;

class ProcessBuffer implements Buffer
{

	/** @var resource */
	private $pointer;

	/**
	 * @param string $command
	 * @param string $mode
	 */
	public function __construct($command, $mode = 'r')
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
	 * @return void
	 */
	public function write($data)
	{
		throw new Exception('Not implemented...');
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
	 * @return int
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
