<?php declare(strict_types = 1);

namespace Tests\Cases\ErrorPresenter;

use Contributte\Application\ErrorPresenter\ModuleErrorPresenterLocator;
use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\TextResponse;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\Presenters\Admin\DashboardPresenter as AdminDashboardPresenter;
use Tests\Fixtures\Presenters\Admin\ErrorPresenter as AdminErrorPresenter;
use Tests\Fixtures\Presenters\Admin\UserPresenter as AdminUserPresenter;
use Tests\Fixtures\Presenters\DefaultErrorPresenter;
use Tests\Fixtures\Presenters\Front\ArticlePresenter as FrontArticlePresenter;
use Tests\Fixtures\Presenters\Front\ErrorPresenter as FrontErrorPresenter;
use Tests\Fixtures\Presenters\Front\HomepagePresenter as FrontHomepagePresenter;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Test simulating real application error handling flow.
 * Presenters throw exceptions, and errors are routed to module-specific error presenters.
 *
 * @testCase
 */
final class ApplicationErrorHandlingTest extends TestCase
{

	private ModuleErrorPresenterLocator $errorLocator;

	/** @var array<string, class-string<IPresenter>> */
	private array $presenterMap;

	protected function setUp(): void
	{
		// Configure error presenter routing
		$this->errorLocator = new ModuleErrorPresenterLocator([
			'Front:*' => FrontErrorPresenter::PRESENTER_NAME,
			'Admin:*' => AdminErrorPresenter::PRESENTER_NAME,
		], DefaultErrorPresenter::PRESENTER_NAME);

		// Map presenter names to classes (simulating PresenterFactory)
		$this->presenterMap = [
			'Front:Homepage' => FrontHomepagePresenter::class,
			'Front:Article' => FrontArticlePresenter::class,
			'Admin:Dashboard' => AdminDashboardPresenter::class,
			'Admin:User' => AdminUserPresenter::class,
			'Front:Error' => FrontErrorPresenter::class,
			'Admin:Error' => AdminErrorPresenter::class,
			'Error' => DefaultErrorPresenter::class,
		];
	}

	/**
	 * Simulates application run: execute presenter, catch error, route to error presenter.
	 */
	private function simulateApplicationRun(Request $request): Response
	{
		$presenterName = $request->getPresenterName();
		$presenter = $this->createPresenter($presenterName);

		try {
			// Try to run the presenter (will throw exception)
			return $presenter->run($request);
		} catch (\Throwable $e) {
			// Error occurred - locate the appropriate error presenter
			$errorPresenterName = $this->errorLocator->locate($request);

			if ($errorPresenterName === null) {
				throw $e; // No error presenter configured
			}

			// Create and run the error presenter
			$errorPresenter = $this->createPresenter($errorPresenterName);
			$errorRequest = new Request(
				$errorPresenterName,
				Request::FORWARD,
				[
					'exception' => $e,
					'request' => $request,
				]
			);

			return $errorPresenter->run($errorRequest);
		}
	}

	private function createPresenter(string $name): IPresenter
	{
		if (!isset($this->presenterMap[$name])) {
			throw new \InvalidArgumentException("Presenter '$name' not found in map");
		}

		$class = $this->presenterMap[$name];

		return new $class();
	}

	/**
	 * Test: Front:Homepage throws error -> Front:Error handles it
	 */
	public function testFrontHomepageErrorRoutesToFrontError(): void
	{
		$request = new Request('Front:Homepage', 'GET', ['action' => 'default']);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Front Error:', (string) $response->getSource());
		Assert::contains('Homepage error occurred', (string) $response->getSource());
	}

	/**
	 * Test: Front:Article throws error -> Front:Error handles it
	 */
	public function testFrontArticleErrorRoutesToFrontError(): void
	{
		$request = new Request('Front:Article', 'GET', ['action' => 'detail', 'id' => 123]);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Front Error:', (string) $response->getSource());
		Assert::contains('Article detail error', (string) $response->getSource());
	}

	/**
	 * Test: Front:Article with invalid ID throws BadRequestException -> Front:Error handles it
	 */
	public function testFrontArticleNotFoundRoutesToFrontError(): void
	{
		$request = new Request('Front:Article', 'GET', ['action' => 'detail', 'id' => -1]);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Front Error:', (string) $response->getSource());
		Assert::contains('Article not found', (string) $response->getSource());
	}

	/**
	 * Test: Admin:Dashboard throws error -> Admin:Error handles it
	 */
	public function testAdminDashboardErrorRoutesToAdminError(): void
	{
		$request = new Request('Admin:Dashboard', 'GET', ['action' => 'default']);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Admin Error:', (string) $response->getSource());
		Assert::contains('Admin dashboard error occurred', (string) $response->getSource());
	}

	/**
	 * Test: Admin:User:list throws error -> Admin:Error handles it
	 */
	public function testAdminUserListErrorRoutesToAdminError(): void
	{
		$request = new Request('Admin:User', 'GET', ['action' => 'list']);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Admin Error:', (string) $response->getSource());
		Assert::contains('User list error', (string) $response->getSource());
	}

	/**
	 * Test: Admin:User:edit throws BadRequestException -> Admin:Error handles it
	 */
	public function testAdminUserNotFoundRoutesToAdminError(): void
	{
		$request = new Request('Admin:User', 'GET', ['action' => 'edit', 'id' => 0]);

		$response = $this->simulateApplicationRun($request);

		Assert::type(TextResponse::class, $response);
		Assert::contains('Admin Error:', (string) $response->getSource());
		Assert::contains('User not found', (string) $response->getSource());
	}

	/**
	 * Test: Verify Front and Admin errors go to different error presenters
	 */
	public function testDifferentModulesRouteToDifferentErrorPresenters(): void
	{
		// Front module error
		$frontRequest = new Request('Front:Homepage', 'GET', ['action' => 'default']);
		$frontResponse = $this->simulateApplicationRun($frontRequest);

		// Admin module error
		$adminRequest = new Request('Admin:Dashboard', 'GET', ['action' => 'default']);
		$adminResponse = $this->simulateApplicationRun($adminRequest);

		// Verify different error presenters handled the errors
		Assert::contains('Front Error:', (string) $frontResponse->getSource());
		Assert::contains('Admin Error:', (string) $adminResponse->getSource());

		// Verify the error messages are from different presenters
		Assert::notContains('Admin Error:', (string) $frontResponse->getSource());
		Assert::notContains('Front Error:', (string) $adminResponse->getSource());
	}

	/**
	 * Test: Error presenter receives original request information
	 */
	public function testErrorPresenterReceivesOriginalRequest(): void
	{
		$originalRequest = new Request('Front:Homepage', 'GET', [
			'action' => 'default',
			'foo' => 'bar',
		]);

		// Create error presenter and track request
		$errorPresenter = new FrontErrorPresenter();

		try {
			$presenter = $this->createPresenter('Front:Homepage');
			$presenter->run($originalRequest);
		} catch (\Throwable $e) {
			$errorRequest = new Request(
				'Front:Error',
				Request::FORWARD,
				[
					'exception' => $e,
					'request' => $originalRequest,
				]
			);

			$errorPresenter->run($errorRequest);

			// Verify error presenter received the exception and original request
			$receivedRequest = $errorPresenter->getRequest();
			Assert::notNull($receivedRequest);
			Assert::type(\Throwable::class, $receivedRequest->getParameter('exception'));
			Assert::same($originalRequest, $receivedRequest->getParameter('request'));
		}
	}

	/**
	 * Test: Complete error handling flow with request parameters preserved
	 */
	public function testCompleteErrorFlowWithParameters(): void
	{
		// Simulate a POST request with data
		$request = new Request('Admin:User', 'POST', [
			'action' => 'edit',
			'id' => -5,
			'name' => 'John Doe',
			'email' => 'john@example.com',
		]);

		$response = $this->simulateApplicationRun($request);

		// Error should be handled by Admin:Error
		Assert::type(TextResponse::class, $response);
		Assert::contains('Admin Error:', (string) $response->getSource());
		Assert::contains('User not found', (string) $response->getSource());
	}

	/**
	 * Test: Sequential errors in same module go to same error presenter
	 */
	public function testSequentialErrorsInSameModule(): void
	{
		$requests = [
			new Request('Front:Homepage', 'GET', ['action' => 'default']),
			new Request('Front:Homepage', 'GET', ['action' => 'about']),
			new Request('Front:Article', 'GET', ['action' => 'detail', 'id' => 1]),
		];

		foreach ($requests as $request) {
			$response = $this->simulateApplicationRun($request);
			Assert::type(TextResponse::class, $response);
			Assert::contains('Front Error:', (string) $response->getSource());
		}
	}

	/**
	 * Test: Mixed module errors route correctly
	 */
	public function testMixedModuleErrorsRouteCorrectly(): void
	{
		$testCases = [
			['Front:Homepage', 'Front Error:'],
			['Admin:Dashboard', 'Admin Error:'],
			['Front:Article', 'Front Error:'],
			['Admin:User', 'Admin Error:'],
		];

		foreach ($testCases as [$presenterName, $expectedPrefix]) {
			$request = new Request($presenterName, 'GET', ['action' => 'default']);
			$response = $this->simulateApplicationRun($request);

			Assert::contains(
				$expectedPrefix,
				(string) $response->getSource(),
				"Expected '$expectedPrefix' for presenter '$presenterName'"
			);
		}
	}

}

(new ApplicationErrorHandlingTest())->run();
