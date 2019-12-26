<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionsGrid;

use Nette\Localization\ITranslator;
use Nette\Utils\Html;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Common\Localization\TranslatorUtils;
use SeStep\Typeful\Latte\PropertyFilter;
use Ublaboo\DataGrid\DataGrid;

class CommissionsGridFactory
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
        $commissionGrid = new DataGrid();

        $commissionGrid->setTranslator($this->translator);

        $commissionGrid->addColumnText('fursuit', 'paf.entity.fursuit')
            ->setRenderer(function (Commission $row) {
                return $row->specification->characterName;
            });
        $commissionGrid->addColumnText('customer', 'commission.customer')
            ->setRenderer(function (Commission $row) {
                return $row->customer->displayName;
            });
        $commissionGrid->addColumnText('status', 'commission.commission.status')
            ->setRenderer(function (Commission $commission) {
                $html = Html::el('div');
                $status = Html::el('span');
                $status->setText($this->translator->translate('commission.commission.status.' . $commission->status));
                $html->addHtml($status);
                if ($commission->archivedOn) {
                    $archivedOn = $this->propertyFilter->displayEntityProperty(
                        $commission->archivedOn,
                        Commission::class,
                        'archivedOn'
                    );
                    $archivedOnEl = Html::el('span');
                    $archivedOnEl->setText($this->translator->translate('commission.commission.archivedOn', [
                        'archivedOn' => $archivedOn,
                    ]));
                    $html->addHtml($archivedOnEl);
                }

                return $html;
            })
            ->setFilterMultiSelect(TranslatorUtils::mapTranslate(
                CommissionWorkflow::getStatusesLocalized(),
                $this->translator
            ));

        $commissionGrid->addColumnDateTime('acceptedOn', 'commission.commission.acceptedOn')
            ->setSortable();
        $commissionGrid->addColumnDateTime('targetDelivery', 'commission.commission.targetDelivery')
            ->setSortable();

        return $commissionGrid;
    }
}
