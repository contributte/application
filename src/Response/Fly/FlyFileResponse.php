<?php

namespace Contributte\Application\Response\Fly;

use Contributte\Application\Response\Fly\Adapter\Adapter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class FlyFileResponse extends FlyResponse
{

	/** @var string */
	private $contentType = 'application/octet-stream';

	/** @var bool */
	private $forceDownload = TRUE;

	/** @var string */
	private $filename;

	/**
	 * @param Adapter $adapter
	 * @param string $filename
	 */
	public function __construct(Adapter $adapter, $filename)
	{
		parent::__construct($adapter);
		$this->filename = $filename;
	}

	/**
	 * @param string $contentType
	 * @return void
	 */
	public function setContentType($contentType)
	{
		$this->contentType = $contentType;
	}

	/**
	 * @param string $filename
	 * @return void
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * @param boolean $force
	 * @return void
	 */
	public function setForceDownload($force = TRUE)
	{
		$this->forceDownload = $force;
	}

	/**
	 * @param IRequest $request
	 * @param IResponse $response
	 * @return void
	 */
	public function send(IRequest $request, IResponse $response)
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
