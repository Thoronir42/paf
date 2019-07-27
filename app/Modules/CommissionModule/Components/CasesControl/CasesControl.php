<?php declare(strict_types=1);

namespace PAF\Modules\Admin\Controls\CasesControl;

use Nette\Application\UI\Control;
use PAF\Modules\CommissionModule\Model\PafCase;

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
