<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CasesGrid;

use Nette\Localization\ITranslator;
use Nette\Utils\Html;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;
use PAF\Common\Localization\TranslatorUtils;
use SeStep\Typeful\Latte\PropertyFilter;
use Ublaboo\DataGrid\DataGrid;

class CasesGridFactory
{
    /** @var ITranslator */
    private $translator;
    /** @var PropertyFilter */
    private $propertyFilter;

    public function __construct(ITranslator $translator, PropertyFilter $propertyFilter)
    {
        $this->translator = $translator;
        $this->propertyFilter = $propertyFilter;
    }

    public function create()
    {
        $casesGrid = new DataGrid();

        $casesGrid->setTranslator($this->translator);

        $casesGrid->addColumnText('fursuit', 'paf.entity.fursuit')
            ->setRenderer(function (PafCase $row) {
                return $row->specification->characterName;
            });
        $casesGrid->addColumnText('customer', 'commission.case.customer')
            ->setRenderer(function (PafCase $row) {
                return $row->customer->displayName;
            });
        $casesGrid->addColumnText('status', 'commission.case.status')
            ->setRenderer(function (PafCase $case) {
                $html = Html::el('div');
                $status = Html::el('span');
                $status->setText($this->translator->translate('commission.case.status.' . $case->status));
                $html->addHtml($status);
                if ($case->archivedOn) {
                    $archivedOn = $this->propertyFilter->displayEntityProperty(
                        $case->archivedOn,
                        PafCase::class,
                        'archivedOn'
                    );
                    $archivedOnEl = Html::el('span');
                    $archivedOnEl->setText($this->translator->translate('commission.case.archivedOn', [
                        'archivedOn' => $archivedOn,
                    ]));
                    $html->addHtml($archivedOnEl);
                }

                return $html;
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
