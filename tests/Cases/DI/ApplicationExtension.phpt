<?php declare(strict_types = 1);

use Contributte\Application\DI\ApplicationExtension;
use Contributte\Application\UI\MagicControl;
use Contributte\Tester\Toolkit;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\DI\TestArticleControlFactory;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @param array<string, mixed> $config
 */
function createContainer(array $config): Container
{
	$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
	$class = $loader->load(function (Compiler $compiler) use ($config): void {
		$compiler->addExtension('application', new ApplicationExtension());
		$compiler->addConfig(['application' => $config]);
	}, md5(serialize($config)));

	return new $class();
}

// Test: Extension with no components configured
Toolkit::test(function (): void {
	$container = createContainer([]);

	Assert::false($container->hasService('application.magicControl'));
});

// Test: Extension with components configured
Toolkit::test(function (): void {
	$container = createContainer([
		'components' => [
			'articles' => TestArticleControlFactory::class,
		],
	]);

	Assert::true($container->hasService('application.magicControl'));

	/** @var MagicControl $magic */
	$magic = $container->getService('application.magicControl');
	Assert::type(MagicControl::class, $magic);
	Assert::same(['articles'], $magic->getFactoryNames());
	Assert::true($magic->hasFactory('articles'));
});

// Test: MagicControl is autowired
Toolkit::test(function (): void {
	$container = createContainer([
		'components' => [
			'articles' => TestArticleControlFactory::class,
		],
	]);

	$magic = $container->getByType(MagicControl::class);
	Assert::type(MagicControl::class, $magic);
});
