<?php

namespace Contributte\Application\Response\Fly\Buffer;

interface Buffer
{

	const BLOCK = 8196;

	/**
	 * @param mixed $data
	 * @return void
	 */
	public function write($data);

	/**
	 * @param int $size
	 * @return mixed
	 */
	public function read($size);

	/**
	 * @return bool
	 */
	public function eof();

	/**
	 * @return bool
	 */
	public function close();

}
