<?php

namespace App\Controls\NavigationMenu;


class NavMenuSeparator implements INavMenuItem
{

	public function getRole()
	{
		return 'separator';
	}
}
