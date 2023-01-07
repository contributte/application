<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette;
use Nette\Application\IResponse;

final class StringResponse implements IResponse
{

	/** @var string */
	private $content;

	/**
	 * Name of downloading file.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Content-Type of the contents.
	 *
	 * @var string
	 */
	private $contentType;

	/**
	 * Contents as http attachment?
	 *
	 * @var bool
	 */
	private $attachment = false;

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

	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
	{
		$httpResponse->setHeader('Content-Type', $this->contentType);
		$httpResponse->setHeader(
			'Content-Disposition',
			($this->attachment ? 'attachment;' : '') . 'filename="' . $this->name . '"'
		);

		echo $this->content;
	}

}
