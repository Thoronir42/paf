<?php

namespace App\Controls\NavigationMenu;

/**
 * @property    string $target
 * @property 	string $caption
 * @property    array $parameters
 *
 * Class NavMenuLink
 * @package App\Controls\NavigationMenu
 */
class NavMenuLink extends ANavMenuItem
{
	protected $target;
	protected $caption;
	protected $parameters;

	public function getRole()
	{
		if($this->hasItems()){
			return 'dropdown';
		} else {
			return 'link';
		}
	}

	/** @return string */
	public function getTarget()
	{
		return $this->target;
	}

	/** @param string $target */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/** @return array */
	public function getParameters()
	{
		return $this->parameters;
	}

	/** @param array $parameters */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}

	/** @return string */
	public function getCaption()
	{
		return $this->caption;
	}

	/** @param string $caption */
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}

}
