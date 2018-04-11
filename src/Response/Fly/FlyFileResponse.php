<?php declare(strict_types = 1);

namespace Contributte\Application\Response\Fly;

use Contributte\Application\Response\Fly\Adapter\Adapter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class FlyFileResponse extends FlyResponse
{

	/** @var string */
	private $contentType = 'application/octet-stream';

	/** @var bool */
	private $forceDownload = true;

	/** @var string */
	private $filename;

	public function __construct(Adapter $adapter, string $filename)
	{
		parent::__construct($adapter);
		$this->filename = $filename;
	}

	public function setContentType(string $contentType): void
	{
		$this->contentType = $contentType;
	}

	public function setFilename(string $filename): void
	{
		$this->filename = $filename;
	}

	public function setForceDownload(bool $force = true): void
	{
		$this->forceDownload = $force;
	}

	public function send(IRequest $request, IResponse $response): void
	{
		$response->setContentType($this->contentType);
		$response->setHeader(
			'Content-Disposition',
			($this->forceDownload ? 'attachment' : 'inline')
			. '; filename="' . $this->filename . '"'
			. '; filename*=utf-8\'\'' . rawurlencode($this->filename)
		);

		$this->adapter->send($request, $response);
	}

}
