<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * @author Jan Galek <admin@gcore.cz>
 */
class XmlResponse extends TextResponse
{

	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->addHeader('Content-Type', 'text/xml');
		parent::send($httpRequest, $httpResponse);
	}

}
