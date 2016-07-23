<?php

namespace App\Controls\NavigationMenu;

use App\Libs\Bootstrap\BootstrapLevels;
use Nette\Application\UI;
use Nette\Object;


/**
 * Class NavigationMenuItem
 * @package App\Controls
 *
 * @property		NavMenuLabel $label
 */
abstract class ANavMenuItem extends Object implements INavMenuItem{
	use NavigationMenuSubitems;

	protected $label;

	/** @return NavMenuLabel */
	public function getLabel()
	{
		return $this->label;
	}



	/** @param NavMenuLabel $label */
	public function setLabel(NavMenuLabel $label)
	{
		$this->label = $label;
	}

	public function addLabel($text, $level = BootstrapLevels::LEVEL_DEFAULT){
		$label = new NavMenuLabel();
		$label->text = $text;
		$label->level = $level;

		$this->setLabel($label);
	}





}
