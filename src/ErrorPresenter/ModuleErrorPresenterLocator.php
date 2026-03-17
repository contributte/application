<?php declare(strict_types = 1);

namespace Contributte\Application\ErrorPresenter;

use Nette\Application\Request;

/**
 * Routes error presenter requests to module-specific error presenters
 * based on configurable patterns.
 *
 * Configuration example:
 *   'Front:*' => 'Front:Error'
 *   'Admin:*' => 'Admin:Error'
 *   'Api:*' => 'Api:Error'
 */
class ModuleErrorPresenterLocator implements ErrorPresenterLocator
{

	/** @var array<string, string> Pattern => ErrorPresenter mapping */
	private array $mapping = [];

	private ?string $fallback = null;

	/**
	 * @param array<string, string> $mapping Pattern => ErrorPresenter mapping (e.g., ['Front:*' => 'Front:Error'])
	 */
	public function __construct(array $mapping = [], ?string $fallback = null)
	{
		$this->mapping = $mapping;
		$this->fallback = $fallback;
	}

	/**
	 * Adds a pattern to error presenter mapping.
	 *
	 * @param string $pattern Pattern to match (e.g., 'Front:*', 'Admin:Module:*')
	 * @param string $errorPresenter Error presenter name (e.g., 'Front:Error')
	 */
	public function addMapping(string $pattern, string $errorPresenter): self
	{
		$this->mapping[$pattern] = $errorPresenter;

		return $this;
	}

	/**
	 * Sets the fallback error presenter used when no pattern matches.
	 */
	public function setFallback(?string $fallback): self
	{
		$this->fallback = $fallback;

		return $this;
	}

	public function getFallback(): ?string
	{
		return $this->fallback;
	}

	/**
	 * @return array<string, string>
	 */
	public function getMapping(): array
	{
		return $this->mapping;
	}

	public function locate(?Request $request): ?string
	{
		if ($request === null) {
			return $this->fallback;
		}

		$presenterName = $request->getPresenterName();

		// Try to match against patterns
		foreach ($this->mapping as $pattern => $errorPresenter) {
			if ($this->matchPattern($presenterName, $pattern)) {
				return $errorPresenter;
			}
		}

		return $this->fallback;
	}

	/**
	 * Matches a presenter name against a pattern.
	 *
	 * Supported patterns:
	 * - 'Front:*' matches 'Front:Homepage', 'Front:Article', 'Front:Sub:Detail'
	 * - 'Admin:User:*' matches 'Admin:User:List', 'Admin:User:Edit'
	 * - 'Api:V1:*' matches 'Api:V1:Users', 'Api:V1:Products'
	 * - Exact match: 'Front:Homepage' matches only 'Front:Homepage'
	 */
	private function matchPattern(string $presenterName, string $pattern): bool
	{
		// Check for wildcard pattern
		if (str_ends_with($pattern, ':*')) {
			$prefix = substr($pattern, 0, -1); // Remove the '*' but keep the ':'

			return str_starts_with($presenterName . ':', $prefix);
		}

		// Exact match
		return $presenterName === $pattern;
	}

}
