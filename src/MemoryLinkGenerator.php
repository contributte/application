<?php

namespace Contributte\Application;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class MemoryLinkGenerator extends LinkGenerator
{

	/** @var array */
	private $cache = [];

	/**
	 * @param string $dest
	 * @param array $params
	 * @return string
	 */
	public function link($dest, array $params = [])
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
