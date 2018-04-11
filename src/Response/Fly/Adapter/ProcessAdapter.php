<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly\Adapter;

use Contributte\Application\Response\Fly\Buffer\ProcessBuffer;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class ProcessAdapter implements Adapter
{

	/** @var string */
	private $command;

	/** @var string */
	private $mode;

	/** @var int */
	private $buffersize;

	public function __construct(string $command, string $mode = 'r', int $buffersize = 8192)
	{
		$this->command = $command;
		$this->mode = $mode;
		$this->buffersize = $buffersize;
	}

	public function send(IRequest $request, IResponse $response): void
	{
		// Open file Buffer
		$b = new ProcessBuffer($this->command, $this->mode);

		while (!$b->eof()) {
			// Read from Buffer
			$output = $b->read($this->buffersize);

			// Goes to ouput
			echo $output;
		}

		// Close file Buffer
		$b->close();
	}

}
