<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Buffer;

interface Buffer
{

	public const BLOCK = 8196;

	public function write(mixed $data): void;

	/**
	 * @param positive-int $size
	 */
	public function read(int $size): mixed;

	public function eof(): bool;

	public function close(): int;

}
