<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Psr\Http\Message\StreamInterface;

/**
 * File download response from PSR7 stream.
 */
final class PSR7StreamResponse implements IResponse
{

	/** @var StreamInterface */
	private $stream;

	/** @var string */
	private $name;

	/** @var string */
	private $contentType;

	/**
	 * @param StreamInterface $stream PSR7   Stream instance
	 * @param string $name Imposed file name
	 * @param ?string $contentType MIME content type
	 */
	public function __construct(StreamInterface $stream, string $name, ?string $contentType = null)
	{
		$this->stream = $stream;
		$this->name = $name;
		$this->contentType = $contentType ?? 'application/octet-stream';
	}

	/**
	 * Returns the stream to a downloaded file.
	 */
	public function getStream(): StreamInterface
	{
		return $this->stream;
	}

	/**
	 * Returns the file name.
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Returns the MIME content type of a downloaded file.
	 */
	public function getContentType(): string
	{
		return $this->contentType;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		// Set response headers for the file download
		$httpResponse->setHeader('Content-Length', (string) $this->stream->getSize());
		$httpResponse->setHeader('Content-Type', $this->contentType);
		$httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $this->name . '";');

		while (!$this->stream->eof()) {
			echo $this->stream->read((int) 4e6);
		}

		$this->stream->close();
	}

}
