<?php

namespace App\Modules\Admin\Controls\CaseControl;


use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\PafCase;
use App\Common\Model\Traits\FursuitEntity;
use Nette\Application\UI\Control;

class PafCaseControl extends Control
{
    public $onCommentAdd = [];
    public $onCommentDelete = [];

    public $onUpdate = [];

    /** @var PafCase */
    private $case;

    public function __construct()
    {
        parent::__construct();
    }

    public function setCase(PafCase $case) {
        $this->case = $case;
    }

    public function renderForm()
    {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/pafCaseControlForm.latte');

        $template->render();
    }

    public function createComponentForm()
    {
        $form = new Form();





        return $form;
    }
}
