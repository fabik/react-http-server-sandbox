<?php

namespace WebServer;

use Kdyby;
use Nette;


class HttpRequestFactory extends Kdyby\Console\HttpRequestFactory
{

	/** @var Nette\Http\Request */
	private $fakeHttpRequest;


	/**
	 * @param Nette\Http\Request $fakeHttpRequest
	 */
	public function setFakeHttpRequest(Nette\Http\Request $fakeHttpRequest)
	{
		$this->fakeHttpRequest = $fakeHttpRequest;
	}


	/**
	 * @return Nette\Http\Request
	 */
	public function createHttpRequest()
	{
		return $this->fakeHttpRequest ?: parent::createHttpRequest();
	}

}
