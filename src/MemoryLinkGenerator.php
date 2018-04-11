<?php declare(strict_types = 1);

namespace Contributte\Application;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class MemoryLinkGenerator extends LinkGenerator
{

	/** @var string[] */
	private $cache = [];

	/**
	 * @param string $dest
	 * @param mixed[] $params
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function link($dest, array $params = []): string
	{
		// Generates cache key
		$cacheKey = md5(serialize([$dest, $params]));

		if (!isset($this->cache[$cacheKey])) {
			// Forward to real link generator
			$this->cache[$cacheKey] = parent::link($dest, $params);
		}

		return $this->cache[$cacheKey];
	}

}
