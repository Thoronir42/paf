<?php

namespace App\Controls\NavigationMenu;
use Nette\UnexpectedValueException;

/**
 * @property ANavMenuItem[] $items
 * Class NavigationMenuSubitems
 * @package App\Controls\NavigationMenu
 */
trait NavigationMenuSubitems
{
	/** @var ANavMenuItem */
	protected $items = [];

	/**
	 * @return bool
	 */
	public function hasItems(){
		return !empty($this->items);
	}

	/**
	 * @return ANavMenuItem[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param ANavMenuItem[] $items
	 */
	public function setItems($items)
	{
		foreach ($items as $item){
			if(!($item instanceof ANavMenuItem)){
				throw new UnexpectedValueException();
			}
		}
		$this->items = $items;
	}

	/**
	 * @param $target
	 * @param $caption
	 * @param array $parameters
	 * @return NavMenuLink
	 */
	public function addLink($target, $caption, $parameters = [])
	{
		$item = new NavMenuLink();

		$item->target = $target;
		$item->caption = $caption;
		$item->parameters = $parameters;

		return $this->items[] = $item;
	}

	public function addSeparator()
	{
		return $this->items[] = new NavMenuSeparator();
	}
}
