<?php declare(strict_types = 1);

namespace Contributte\Application;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface ILinkGenerator
{

	/**
	 * @param string $dest in format "[[[module:]presenter:]action] [#fragment]"
	 * @param mixed[] $params
	 * @return string
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function link($dest, array $params = []);

}
