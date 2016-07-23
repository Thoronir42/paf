<?php

namespace App\Controls\NavigationMenu;


use App\Libs\Bootstrap\BootstrapLevels;
use Nette\InvalidArgumentException;
use Nette\Object;

/**
 * Class NavMenuLabel
 * @package App\Controls\NavigationMenu
 *
 * @property		string $text
 * @property		string $level
 */
class NavMenuLabel extends Object
{
	protected $text;
	protected $level;

	/** @return string */
	public function getText()
	{
		return $this->text;
	}

	/** @param string $text */
	public function setText($text)
	{
		$this->text = $text;
	}

	/** @return string */
	public function getLevel()
	{
		return $this->level;
	}

	/** @param string $level */
	public function setLevel($level)
	{
		if(!array_key_exists($level, BootstrapLevels::getLevels())){
			throw new InvalidArgumentException();
		}

		$this->level = $level;
	}


}
