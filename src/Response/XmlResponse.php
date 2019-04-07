<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Application\UI\ITemplate;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;

class XmlResponse implements IResponse
{

	/** @var mixed */
	private $source;

	/**
	 * @param mixed $source
	 */
	public function __construct($source)
	{
		$this->source = $source;
	}

	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->source;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		$httpResponse->addHeader('Content-Type', 'text/xml');

		if ($this->source instanceof ITemplate) {
			$this->source->render();
		} else {
			echo $this->source;
		}
	}

}
