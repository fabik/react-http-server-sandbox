<?php

namespace WebServer;

use Kdyby;
use Nette;


class CliRouter extends Kdyby\Console\CliRouter
{

	/** @var bool */
	private $isCli;


	/**
	 * @param Nette\DI\Container $container
	 */
	public function __construct(Nette\DI\Container $container)
	{
		parent::__construct($container);
		$this->isCli = defined('PHP_SAPI') && PHP_SAPI === 'cli';
	}


	/**
	 * @param bool $isCli
	 */
	public function setIsCli($isCli)
	{
		$this->isCli = (bool) $isCli;
	}


	/**
	 * @param Nette\Http\IRequest $httpRequest
	 * @return Nette\Application\Request|NULL
	 */
	public function match(Nette\Http\IRequest $httpRequest)
	{
		return $this->isCli ? parent::match($httpRequest) : NULL;
	}

}
