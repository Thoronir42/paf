<?php

namespace App\Modules\Admin\Presenters;


use App\Common\BasePresenter;

abstract class AdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();

        $this->template->background_color = '#7F007F';
    }
}
