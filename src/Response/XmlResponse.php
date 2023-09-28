<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Response;
use Nette\Application\UI\Template;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;

class XmlResponse implements Response
{

	private string|Template $source;

	public function __construct(string|Template $source)
	{
		$this->source = $source;
	}

	public function getSource(): string|Template
	{
		return $this->source;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		$httpResponse->addHeader('Content-Type', 'text/xml');

		if ($this->source instanceof Template) {
			$this->source->render();
		} else {
			echo $this->source;
		}
	}

}
