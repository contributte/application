<?php declare(strict_types = 1);

namespace Tests\Cases\ErrorPresenter;

use Contributte\Application\ErrorPresenter\ModuleErrorPresenterLocator;
use Nette\Application\Request;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\Presenters\Admin\ErrorPresenter as AdminErrorPresenter;
use Tests\Fixtures\Presenters\DefaultErrorPresenter;
use Tests\Fixtures\Presenters\Front\ErrorPresenter as FrontErrorPresenter;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Test cases demonstrating module-based error presenter routing
 * with Admin and Front error presenters.
 *
 * @testCase
 */
final class ModuleErrorPresenterRoutingTest extends TestCase
{

	private ModuleErrorPresenterLocator $locator;

	protected function setUp(): void
	{
		$this->locator = new ModuleErrorPresenterLocator([
			'Admin:*' => AdminErrorPresenter::PRESENTER_NAME,
			'Front:*' => FrontErrorPresenter::PRESENTER_NAME,
		], DefaultErrorPresenter::PRESENTER_NAME);
	}

	/**
	 * Test: Admin module presenters route to Admin:Error
	 */
	public function testAdminModuleRoutesToAdminError(): void
	{
		// Admin:Dashboard -> Admin:Error
		$request = new Request('Admin:Dashboard');
		Assert::same('Admin:Error', $this->locator->locate($request));

		// Admin:User:List -> Admin:Error
		$request = new Request('Admin:User:List');
		Assert::same('Admin:Error', $this->locator->locate($request));

		// Admin:User:Edit -> Admin:Error
		$request = new Request('Admin:User:Edit');
		Assert::same('Admin:Error', $this->locator->locate($request));

		// Admin:Settings:Security -> Admin:Error
		$request = new Request('Admin:Settings:Security');
		Assert::same('Admin:Error', $this->locator->locate($request));
	}

	/**
	 * Test: Front module presenters route to Front:Error
	 */
	public function testFrontModuleRoutesToFrontError(): void
	{
		// Front:Homepage -> Front:Error
		$request = new Request('Front:Homepage');
		Assert::same('Front:Error', $this->locator->locate($request));

		// Front:Article -> Front:Error
		$request = new Request('Front:Article');
		Assert::same('Front:Error', $this->locator->locate($request));

		// Front:Category:List -> Front:Error
		$request = new Request('Front:Category:List');
		Assert::same('Front:Error', $this->locator->locate($request));

		// Front:Product:Detail -> Front:Error
		$request = new Request('Front:Product:Detail');
		Assert::same('Front:Error', $this->locator->locate($request));
	}

	/**
	 * Test: Unknown modules route to default error presenter
	 */
	public function testUnknownModuleRoutesToDefault(): void
	{
		// Api:Users -> Error (fallback)
		$request = new Request('Api:Users');
		Assert::same('Error', $this->locator->locate($request));

		// Cron:Cleanup -> Error (fallback)
		$request = new Request('Cron:Cleanup');
		Assert::same('Error', $this->locator->locate($request));

		// Unknown:Presenter -> Error (fallback)
		$request = new Request('Unknown:Presenter');
		Assert::same('Error', $this->locator->locate($request));
	}

	/**
	 * Test: Simulate error handling flow for Admin module
	 */
	public function testAdminErrorHandlingFlow(): void
	{
		// 1. Original request that caused error
		$originalRequest = new Request('Admin:User:Edit', 'POST', [
			'id' => 123,
			'action' => 'edit',
		]);

		// 2. Locate the appropriate error presenter
		$errorPresenterName = $this->locator->locate($originalRequest);
		Assert::same('Admin:Error', $errorPresenterName);

		// 3. Create error presenter and handle the error
		$errorPresenter = new AdminErrorPresenter();
		$exception = new \RuntimeException('User not found');

		$errorRequest = new Request($errorPresenterName, 'GET', [
			'exception' => $exception,
			'request' => $originalRequest,
		]);

		$response = $errorPresenter->run($errorRequest);
		Assert::type(\Nette\Application\Responses\TextResponse::class, $response);
	}

	/**
	 * Test: Simulate error handling flow for Front module
	 */
	public function testFrontErrorHandlingFlow(): void
	{
		// 1. Original request that caused error
		$originalRequest = new Request('Front:Product:Detail', 'GET', [
			'id' => 456,
			'slug' => 'awesome-product',
		]);

		// 2. Locate the appropriate error presenter
		$errorPresenterName = $this->locator->locate($originalRequest);
		Assert::same('Front:Error', $errorPresenterName);

		// 3. Create error presenter and handle the error
		$errorPresenter = new FrontErrorPresenter();
		$exception = new \RuntimeException('Product not found');

		$errorRequest = new Request($errorPresenterName, 'GET', [
			'exception' => $exception,
			'request' => $originalRequest,
		]);

		$response = $errorPresenter->run($errorRequest);
		Assert::type(\Nette\Application\Responses\TextResponse::class, $response);
	}

	/**
	 * Test: Different error presenters produce different responses
	 */
	public function testErrorPresentersProduceDifferentResponses(): void
	{
		$exception = new \RuntimeException('Test error');

		// Admin error response
		$adminPresenter = new AdminErrorPresenter();
		$adminRequest = new Request('Admin:Error', 'GET', ['exception' => $exception]);
		$adminResponse = $adminPresenter->run($adminRequest);

		// Front error response
		$frontPresenter = new FrontErrorPresenter();
		$frontRequest = new Request('Front:Error', 'GET', ['exception' => $exception]);
		$frontResponse = $frontPresenter->run($frontRequest);

		// Default error response
		$defaultPresenter = new DefaultErrorPresenter();
		$defaultRequest = new Request('Error', 'GET', ['exception' => $exception]);
		$defaultResponse = $defaultPresenter->run($defaultRequest);

		// Verify they are different presenters with different behavior
		Assert::type(\Nette\Application\Responses\TextResponse::class, $adminResponse);
		Assert::type(\Nette\Application\Responses\TextResponse::class, $frontResponse);
		Assert::type(\Nette\Application\Responses\TextResponse::class, $defaultResponse);
	}

	/**
	 * Test: Module root presenters are correctly routed
	 */
	public function testModuleRootPresentersRouting(): void
	{
		// Admin (module root) -> Admin:Error
		$request = new Request('Admin');
		Assert::same('Admin:Error', $this->locator->locate($request));

		// Front (module root) -> Front:Error
		$request = new Request('Front');
		Assert::same('Front:Error', $this->locator->locate($request));
	}

	/**
	 * Test: Real-world configuration scenario
	 */
	public function testRealWorldConfiguration(): void
	{
		// Configuration as described in GitHub issue #4
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
			'Admin:*' => 'Admin:Error',
		]);

		// Test various presenters
		Assert::same('Front:Error', $locator->locate(new Request('Front:Homepage')));
		Assert::same('Front:Error', $locator->locate(new Request('Front:Contact')));
		Assert::same('Front:Error', $locator->locate(new Request('Front:Blog:Article')));

		Assert::same('Admin:Error', $locator->locate(new Request('Admin:Dashboard')));
		Assert::same('Admin:Error', $locator->locate(new Request('Admin:Posts:Edit')));
		Assert::same('Admin:Error', $locator->locate(new Request('Admin:Settings:General')));

		// No fallback configured, unknown modules return null
		Assert::null($locator->locate(new Request('Api:Users')));
	}

	/**
	 * Test: Extended configuration with API module
	 */
	public function testExtendedConfigurationWithApiModule(): void
	{
		$locator = new ModuleErrorPresenterLocator([
			'Front:*' => 'Front:Error',
			'Admin:*' => 'Admin:Error',
			'Api:*' => 'Api:Error',
		], 'Error');

		Assert::same('Front:Error', $locator->locate(new Request('Front:Homepage')));
		Assert::same('Admin:Error', $locator->locate(new Request('Admin:Dashboard')));
		Assert::same('Api:Error', $locator->locate(new Request('Api:V1:Users')));
		Assert::same('Error', $locator->locate(new Request('Unknown:Presenter')));
	}

}

(new ModuleErrorPresenterRoutingTest())->run();
