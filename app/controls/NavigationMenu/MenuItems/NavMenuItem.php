<?php

namespace App\Controls\NavigationMenu;


use Nette\Object;

/**
 * Class NavigationMenuItem
 * @package App\Controls
 *
 * @property $code
 * @property string $caption
 * @property NavMenuItem[] $items
 * @property NavMenuLabel $label
 *
 */
class NavMenuItem extends Object{

	/** @var string */
	protected $code;

	/** @var string */
	protected $caption;

	/** @var NavMenuItem[]  */
	private $items;

	/** @var NavMenuLabel|null  */
	private $label;

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getCaption()
	{
		return $this->caption;
	}

	/**
	 * @param string $caption
	 */
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}

	/**
	 * @return NavMenuLabel|null
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param NavMenuLabel $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}


	/**
	 * @return bool
	 */
	public function hasItems(){
		return !empty($this->items);
	}

	/**
	 * @return NavMenuItem[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param NavMenuItem[] $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * @param $code
	 * @param $caption
	 */
	public function addItem($code, $caption, NavMenuLabel $label = null)
	{
		$item = new NavMenuItem;
		$item->code = $code;
		$item->caption = $caption;
		$item->label = $label;

		$this->items[] = $item;

		return $item;
	}

	public function addSeparator()
	{
		$this->addItem(null, '');
	}

	/**
	 * @return bool
	 */
	public function isSeparator()
	{
		return !$this->code && !$this->label;
	}




}
