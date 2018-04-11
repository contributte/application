<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Responses\JsonResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Nette\Utils\Json;

/**
 * Add JSON_PRETTY_PRINT option to Nette\Application\Responses\JsonResponse
 *
 * @author Pavel Janda <me@paveljanda.com>
 */
class JsonPrettyResponse extends JsonResponse
{

	/** @var int */
	private $code = IHttpResponse::S200_OK;

	public function setCode(int $code): void
	{
		$this->code = $code;
	}

	public function getCode(): int
	{
		return $this->code;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->getContentType(), 'utf-8');
		$httpResponse->setExpiration(false);
		$httpResponse->setCode($this->code);

		echo Json::encode($this->getPayload(), Json::PRETTY);
	}

}
