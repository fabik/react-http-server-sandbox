<?php

namespace WebServer;

use Nette;
use React;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


class RunCommand extends Command
{

	/** @var InputInterface */
	private $input;

	/** @var OutputInterface */
	private $output;

	/** @var ContainerFactory */
	private $containerFactory;


	public function __construct(ContainerFactory $containerFactory)
	{
		parent::__construct();
		$this->containerFactory = $containerFactory;
	}


	protected function configure()
	{
		$this->setName('webserver:run');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;

		$loop = React\EventLoop\Factory::create();
		$socket = new React\Socket\Server($loop);

		$http = new React\Http\Server($socket);
		$http->on('request', function(React\Http\Request $request, React\Http\Response $response) {
			$this->handleRequest($request, $response);
		});

		$socket->listen(8000);
		$loop->run();
	}


	private function handleRequest(React\Http\Request $request, React\Http\Response $response)
	{
		try {
			$container = $this->containerFactory->createContainer();

			/** @var HttpRequestFactory $requestFactory */
			$requestFactory = $container->getByType(HttpRequestFactory::class);
			$requestFactory->setFakeHttpRequest($this->createNetteHttpRequest($request));

			/** @var CliRouter $router */
			$cliRouter = $container->getService('console.router');
			$cliRouter->setIsCli(FALSE);

			/** @var Nette\Application\Application $application */
			$application = $container->getByType(Nette\Application\Application::class);
			array_unshift($application->onError, function() { throw new AbortException; });

			ob_start();
			$application->run();
			$responseBody = ob_get_contents();
			ob_end_clean();

			$response->writeHead(200, array('Content-Type' => 'text/html; charset=utf-8'));
			$response->end($responseBody);
		} catch (\Exception $e) {
			Debugger::log($e, Debugger::EXCEPTION);
			$response->writeHead(500, array('Content-Type' => 'text/plain'));
			$response->end('Internal Server Error');
		}
	}


	/**
	 * @param React\Http\Request $reactRequest
	 * @return Nette\Http\Request
	 */
	private function createNetteHttpRequest(React\Http\Request $reactRequest)
	{
		$url = new Nette\Http\UrlScript('http://' . $reactRequest->getHeaders()['Host'] . $reactRequest->getPath());
		$url->setQuery($reactRequest->getQuery());

		return new Nette\Http\Request(
			$url,
			NULL,
			NULL,
			NULL,
			NULL,
			$reactRequest->getHeaders(),
			$reactRequest->getMethod(),
			$reactRequest->remoteAddress,
			NULL,
			NULL
		);
	}

}
