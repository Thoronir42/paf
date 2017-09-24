<?php

namespace App\Modules\Admin\Controls\CasesControl;


use App\Common\Model\Entity\PafCase;
use Nette\Application\UI\Control;

class CasesControl extends Control
{
    private $cases;

    /**
     * @param PafCase[] $cases
     */
    public function setCases($cases)
    {
        $this->cases = $cases;
    }

    public function renderList()
    {
        $template = $this->createTemplate();

        $template->cases = $this->cases;
        $template->setFile(__DIR__ . '/casesControlList.latte');

        $template->render();
    }
}
