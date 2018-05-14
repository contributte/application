<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette;
use Nette\Application\Responses\TextResponse;

/**
 * @author Jan Galek <admin@gcore.cz>
 */
class XmlResponse extends TextResponse
{
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
	{
		$httpResponse->addHeader('Content-Type', 'text/xml');
		parent::send($httpRequest, $httpResponse);
	}
}
