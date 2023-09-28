<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Response;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;
use Nette\Utils\Json;

class JsonPrettyResponse implements Response
{

	private int $code = HttpResponse::S200_OK;

	private mixed $payload;

	private string $contentType;

	private ?string $expiration = null;

	public function __construct(mixed $payload, ?string $contentType = null)
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

	public function getPayload(): mixed
	{
		return $this->payload;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->getContentType(), 'utf-8');
		$httpResponse->setExpiration($this->expiration);
		$httpResponse->setCode($this->code);

		echo Json::encode($this->getPayload(), pretty: true);
	}

}
