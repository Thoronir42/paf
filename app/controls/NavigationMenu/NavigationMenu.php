<?php

namespace App\Controls\NavigationMenu;

use Nette\Application\UI;

/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 17.06.2016
 * Time: 14:13
 */
class NavigationMenu extends UI\Control
{

	protected $title;

	protected $brand_code;

	/** @var NavMenuItem[]  */
	private $items;

	public function __construct()
	{
		parent::__construct();

		$this->title = '';
		$this->brand_code = 'Default:';
		$this->items = [];

	}

	public function addItem($code, $caption)
	{
		$item = new NavMenuItem();
		$item->code = $code;
		$item->caption = $caption;

		return $this->items[] = $item;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setBrandAction($code)
	{
		$this->brand_code = $code;
	}



	public function renderTop(){
		$this->template->setFile(__DIR__ . "/topMenu.latte");

		$this->template->title = $this->title;
		$this->template->brand_code = $this->brand_code;
		$this->template->items = $this->items;

		$this->template->render();
	}
}

interface INavigationMenuFactory{

	/** @return NavigationMenu */
	public function create();
}
