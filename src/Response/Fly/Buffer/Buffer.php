<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

interface Buffer
{

	public const BLOCK = 8196;

	/**
	 * @param mixed $data
	 */
	public function write($data): void;

	/**
	 * @return mixed
	 */
	public function read(int $size);

	public function eof(): bool;

	public function close(): int;

}
