<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CasesGrid;

use Nette\Localization\ITranslator;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;
use PAF\Utils\TranslatorUtils;
use Ublaboo\DataGrid\DataGrid;

class CasesGridFactory
{
    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function create()
    {
        $casesGrid = new DataGrid();

        $casesGrid->setTranslator($this->translator);

        $casesGrid->addColumnText('customer', 'commission.case.customer')
            ->setRenderer(function (PafCase $row) {
                return $row->customer->displayName;
            });
        $casesGrid->addColumnText('status', 'commission.case.status')
            ->setRenderer(function (PafCase $case) {
                return $this->translator->translate('commission.case.status.' . $case->status);
            })
            ->setFilterMultiSelect(TranslatorUtils::mapTranslate(
                PafCaseWorkflow::getCaseStatesLocalized(),
                $this->translator
            ));

        $casesGrid->addColumnDateTime('acceptedOn', 'commission.case.acceptedOn')
            ->setSortable();
        $casesGrid->addColumnDateTime('targetDelivery', 'commission.case.targetDelivery')
            ->setSortable();

        return $casesGrid;
    }
}
