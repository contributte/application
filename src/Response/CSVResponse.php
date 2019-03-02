<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Tracy\Debugger;

/**
 * CSV file download response
 */
class CSVResponse implements IResponse
{

	/** @var string */
	protected $contentType = 'application/octet-stream';

	/** @var string */
	protected $delimiter;

	/** @var mixed[] */
	protected $data;

	/** @var string */
	protected $outputEncoding;

	/** @var bool */
	protected $includeBom;

	/** @var string */
	protected $name;

	/** @var string[] */
	protected $headers = [
		'Expires' => '0',
		'Cache-Control' => 'no-cache',
		'Pragma' => 'Public',
	];

	/**
	 * @param mixed[] $data
	 */
	public function __construct(
		array $data,
		string $name = 'export.csv',
		string $outputEncoding = 'utf-8',
		string $delimiter = ';',
		bool $includeBom = false
	)
	{
		if (strpos($name, '.csv') === false) {
			$name = sprintf('%s.csv', $name);
		}

		$this->name = $name;
		$this->delimiter = $delimiter;
		$this->data = $data;
		$this->outputEncoding = $outputEncoding;
		$this->includeBom = $includeBom;
	}

	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse): void
	{
		// Disable tracy bar
		if (class_exists(Debugger::class)) {
			Debugger::$productionMode = true;
		}

		// Set Content-Type header
		$httpResponse->setContentType($this->contentType, $this->outputEncoding);

		// Set Content-Disposition header
		$httpResponse->setHeader('Content-Disposition', sprintf('attachment; filename="%s"', $this->name));

		// Set other headers
		foreach ($this->headers as $key => $value) {
			$httpResponse->setHeader($key, $value);
		}

		if (function_exists('ob_start')) {
			ob_start();
		}

		// Output data
		if ($this->includeBom && strtolower($this->outputEncoding) === 'utf-8') {
			echo b"\xEF\xBB\xBF";
		}

		$delimiter = '"' . $this->delimiter . '"';

		foreach ($this->data as $row) {
			if (strtolower($this->outputEncoding) === 'utf-8') {
				echo '"' . implode($delimiter, (array) $row) . '"';
			} else {
				echo iconv('UTF-8', $this->outputEncoding, '"' . implode($delimiter, (array) $row) . '"');
			}

			echo "\r\n";
		}

		if (function_exists('ob_end_flush')) {
			ob_end_flush();
		}
	}

}
