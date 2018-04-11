<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

interface Buffer
{

	public const BLOCK = 8196;

	/**
	 * @param mixed $data
	 * @return void
	 */
	public function write($data): void;

	/**
	 * @param int $size
	 * @return mixed
	 */
	public function read(int $size);

	/**
	 * @return bool
	 */
	public function eof(): bool;

	/**
	 * @return int
	 */
	public function close(): int;

}
