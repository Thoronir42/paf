<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Services\Doctrine\Quotes;

class CasesPresenter extends AdminPresenter
{
    /** @var Quotes @inject */
    public $quotes;

    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview();

    }
}
