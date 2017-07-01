<?php

namespace App\Modules\Admin\Presenters;


class CasesPresenter extends AdminPresenter
{
    public function actionList() {
        $this->template->cases = [];
    }
}
