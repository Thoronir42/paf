<?php

namespace App\Controls\NavigationMenu;


use Nette\Object;

class NavMenuLabel extends Object
{
	protected $text;

	protected $class;

	/**
	 * NavigationMenuLabel constructor.
	 * @param string $text
	 * @param string $class
	 */
	public function __construct($text = '0', $class = 'label-default')
	{
		$this->text = $text;
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function setClass($class)
	{
		$this->class = $class;
	}


}
