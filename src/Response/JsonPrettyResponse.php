<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Nette\Utils\Json;

class JsonPrettyResponse implements IResponse
{

	/** @var int */
	private $code = IHttpResponse::S200_OK;

	/** @var mixed */
	private $payload;

	/** @var string */
	private $contentType;

	/** @var string|null */
	private $expiration;

	/**
	 * @param mixed $payload
	 */
	public function __construct($payload, ?string $contentType = null)
	{
		$this->payload = $payload;
		$this->contentType = $contentType ?? 'application/json';
	}

	public function setCode(int $code): void
	{
		$this->code = $code;
	}

	public function getCode(): int
	{
		return $this->code;
	}

	public function getContentType(): string
	{
		return $this->contentType;
	}

	public function setContentType(string $contentType): void
	{
		$this->contentType = $contentType;
	}

	public function getExpiration(): ?string
	{
		return $this->expiration;
	}

	public function setExpiration(?string $expiration): void
	{
		$this->expiration = $expiration;
	}

	/**
	 * @return mixed
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->getContentType(), 'utf-8');
		$httpResponse->setExpiration($this->expiration);
		$httpResponse->setCode($this->code);

		echo Json::encode($this->getPayload(), Json::PRETTY);
	}

}
