<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\Response;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;
use Nette\InvalidArgumentException;
use Nette\Utils\Image;
use Nette\Utils\ImageType;

class ImageResponse implements Response
{

	private Image|string $image;

	/** @var ImageType::* */
	private int $type;

	private ?int $quality = null;

	/**
	 * @param ImageType::* $type
	 */
	public function __construct(Image|string $image, int $type = Image::JPEG, ?int $quality = null)
	{
		if (!$image instanceof Image && !file_exists($image)) {
			throw new InvalidArgumentException('Image must be Nette\Utils\Image or file path');
		}

		$this->image = $image;
		$this->type = $type;
		$this->quality = $quality;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		$image = $this->image instanceof Image ? $this->image : Image::fromFile($this->image);
		$image->send($this->type, $this->quality);
	}

}
