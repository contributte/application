<?php declare(strict_types = 1);

namespace Tests\Cases\ErrorPresenter;

use Contributte\Application\ErrorPresenter\ModuleErrorPresenterLocator;
use Nette\Application\Request;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ModuleErrorPresenterLocatorTest extends TestCase
{

	public function testLocateWithWildcardPattern(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
			'Admin:*' => 'Admin:Error',
		]);

		$request = new Request('Front:Homepage');
		Assert::same('Front:Error', $locator->locate($request));

		$request = new Request('Front:Article');
		Assert::same('Front:Error', $locator->locate($request));

		$request = new Request('Admin:Dashboard');
		Assert::same('Admin:Error', $locator->locate($request));

		$request = new Request('Admin:User:Edit');
		Assert::same('Admin:Error', $locator->locate($request));
	}

	public function testLocateWithNestedModulePattern(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Api:V1:*' => 'Api:V1:Error',
			'Api:V2:*' => 'Api:V2:Error',
			'Api:*' => 'Api:Error',
		]);

		// More specific pattern should match first (depends on array order)
		$request = new Request('Api:V1:Users');
		Assert::same('Api:V1:Error', $locator->locate($request));

		$request = new Request('Api:V2:Products');
		Assert::same('Api:V2:Error', $locator->locate($request));
	}

	public function testLocateWithExactMatch(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:Homepage' => 'Front:HomepageError',
			'Front:*' => 'Front:Error',
		]);

		// Exact match should win when it comes first
		$request = new Request('Front:Homepage');
		Assert::same('Front:HomepageError', $locator->locate($request));

		$request = new Request('Front:Article');
		Assert::same('Front:Error', $locator->locate($request));
	}

	public function testLocateWithFallback(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Admin:*' => 'Admin:Error',
		], 'Error');

		// No match, should return fallback
		$request = new Request('Front:Homepage');
		Assert::same('Error', $locator->locate($request));

		// Match found
		$request = new Request('Admin:Dashboard');
		Assert::same('Admin:Error', $locator->locate($request));
	}

	public function testLocateWithNullRequest(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
		], 'DefaultError');

		Assert::same('DefaultError', $locator->locate(null));
	}

	public function testLocateWithNullRequestAndNoFallback(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
		]);

		Assert::null($locator->locate(null));
	}

	public function testLocateWithNoMatchAndNoFallback(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Admin:*' => 'Admin:Error',
		]);

		$request = new Request('Front:Homepage');
		Assert::null($locator->locate($request));
	}

	public function testAddMapping(): void
	{
		$locator = new ModuleErrorPresenterLocator();
		$locator->addMapping('Front:*', 'Front:Error');
		$locator->addMapping('Admin:*', 'Admin:Error');

		$request = new Request('Front:Homepage');
		Assert::same('Front:Error', $locator->locate($request));

		$request = new Request('Admin:Dashboard');
		Assert::same('Admin:Error', $locator->locate($request));
	}

	public function testSetFallback(): void
	{
		$locator = new ModuleErrorPresenterLocator();
		$locator->setFallback('Error');

		$request = new Request('Unknown:Presenter');
		Assert::same('Error', $locator->locate($request));
	}

	public function testGetMapping(): void
	{
		$mapping = [
			'Front:*' => 'Front:Error',
			'Admin:*' => 'Admin:Error',
		];

		$locator = new ModuleErrorPresenterLocator($mapping);
		Assert::same($mapping, $locator->getMapping());
	}

	public function testGetFallback(): void
	{
		$locator = new ModuleErrorPresenterLocator([], 'Error');
		Assert::same('Error', $locator->getFallback());

		$locator = new ModuleErrorPresenterLocator();
		Assert::null($locator->getFallback());
	}

	public function testFluentInterface(): void
	{
		$locator = new ModuleErrorPresenterLocator();
		$result = $locator
			->addMapping('Front:*', 'Front:Error')
			->setFallback('Error');

		Assert::same($locator, $result);
	}

	public function testEmptyMapping(): void
	{
		$locator = new ModuleErrorPresenterLocator();

		$request = new Request('Front:Homepage');
		Assert::null($locator->locate($request));
	}

	public function testModuleOnlyPattern(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
		]);

		// Pattern 'Front:*' should also match 'Front' itself when it's a module root
		$request = new Request('Front');
		Assert::same('Front:Error', $locator->locate($request));
	}

}

(new ModuleErrorPresenterLocatorTest())->run();
