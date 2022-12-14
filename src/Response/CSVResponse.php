<?php declare(strict_types = 1);

namespace Contributte\Application\Response;

use Nette\Application\IResponse;
use Nette\Http\IRequest as IHttpRequest;
use Nette\Http\IResponse as IHttpResponse;
use Nette\InvalidStateException;
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
	 * @param mixed[] $data Input data
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
		if ($this->includeBom) {
			echo $this->getBom();
		}

		foreach ($this->data as $row) {
			$csvRow = $this->printCsv($row);

			if (strtolower($this->outputEncoding) === 'utf-8') {
				echo $csvRow;
			} elseif (strtolower($this->outputEncoding) === 'windows-1250') {
				echo iconv('utf-8', $this->outputEncoding, $csvRow);
			} else {
				echo mb_convert_encoding($csvRow, $this->outputEncoding);
			}
		}

		if (function_exists('ob_end_flush')) {
			ob_end_flush();
		}
	}

	private function getBom(): string
	{
		switch (strtolower($this->outputEncoding)) {
			case 'utf-8':
				return b"\xEF\xBB\xBF";

			case 'utf-16':
				return b"\xFF\xFE";

			default:
				return '';
		}
	}

	/**
	 * @param mixed[] $row
	 */
	private function printCsv(array $row): string
	{
		$out = fopen('php://memory', 'wb+');

		if ($out === false) {
			throw new InvalidStateException('Unable to open memory stream');
		}

		fputcsv($out, $row, $this->delimiter);
		rewind($out);
		$c = stream_get_contents($out);
		fclose($out);

		if ($c === false) {
			throw new InvalidStateException('Unable to read from memory stream');
		}

		return $c;
	}

}
