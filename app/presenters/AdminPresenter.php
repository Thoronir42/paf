<?php

namespace App\Presenters;


abstract class AdminPresenter extends BasePresenter
{
	public function startup()
	{
		parent::startup();

		$this->template->background_color = '#7F007F';
	}
}
