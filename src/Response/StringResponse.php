<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Response;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;

final class StringResponse implements Response
{

	private string $content;

	/**
	 * Name of downloading file.
	 */
	private string $name;

	/**
	 * Content-Type of the contents.
	 */
	private string $contentType;

	/**
	 * Contents as http attachment?
	 */
	private bool $attachment = false;

	public function __construct(string $content, string $name, string $contentType = 'text/plain')
	{
		$this->content = $content;
		$this->name = $name;
		$this->contentType = $contentType;
	}

	public function setAttachment(bool $attachment = true): self
	{
		$this->attachment = $attachment;

		return $this;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		$httpResponse->setHeader('Content-Type', $this->contentType);
		$httpResponse->setHeader(
			'Content-Disposition',
			($this->attachment ? 'attachment;' : '') . 'filename="' . $this->name . '"'
		);

		echo $this->content;
	}

}
