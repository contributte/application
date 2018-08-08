<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Nette\InvalidArgumentException;
use Nette\Utils\Image;

/**
 * @author Pavel Janda <me@paveljanda.com>
 */
class ImageResponse implements IResponse
{

	/** @var Image|string */
	private $image;

	/** @var int */
	private $type;

	/** @var int|null */
	private $quality;

	/**
	 * @param Image|string $image
	 * @param int $type
	 * @param int $quality
	 */
	public function __construct($image, $type = Image::JPEG, $quality = null)
	{
		if (!$image instanceof Image && !file_exists($image)) {
			throw new InvalidArgumentException('Image must be Nette\Utils\Image or file path');
		}

		$this->image = $image;
		$this->type = $type;
		$this->quality = $quality;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		if ($this->image instanceof Image) {
			$image = $this->image;
		} else {
			$image = Image::fromFile($this->image);
		}

		$image->send($this->type, $this->quality);
	}

}
