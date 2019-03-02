<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Nette\InvalidArgumentException;
use Nette\Utils\Image;

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
	 */
	public function __construct($image, int $type = Image::JPEG, ?int $quality = null)
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
