<?php

namespace WebServer;

use Nette\Configurator;
use Nette\DI\Container;


class ContainerFactory
{

	/** @var Configurator */
	private $configurator;


	/**
	 * @param Configurator $configurator
	 */
	public function setConfigurator(Configurator $configurator)
	{
		$this->configurator = $configurator;
	}


	/**
	 * @return Container
	 */
	public function createContainer()
	{
		return $this->configurator->createContainer();
	}

}
