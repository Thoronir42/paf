<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionsGrid;

use LeanMapper\Fluent;
use Nette\Application\UI\ITemplateFactory;
use Nette\Localization\ITranslator;
use PAF\Common\Lean\LeanQueryFilter;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Common\Localization\TranslatorUtils;
use Ublaboo\DataGrid\DataGrid;

class CommissionsGridFactory
{
    private ITranslator $translator;
    private ITemplateFactory $templateFactory;
    private LeanQueryFilter $queryFilter;

    public function __construct(
        ITranslator $translator,
        ITemplateFactory $templateFactory,
        LeanQueryFilter $queryFilter
    ) {
        $this->translator = $translator;
        $this->templateFactory = $templateFactory;
        $this->queryFilter = $queryFilter;
    }

    public function create()
    {
        $commissionGrid = new DataGrid();

        $commissionGrid->setTranslator($this->translator);

        $commissionGrid->addColumnText('fursuit', 'paf.entity.fursuit')
            ->setRenderer(fn(Commission $row) => $row->specification->characterName);
        $commissionGrid->addColumnText('customer', 'commission.customer')
            ->setRenderer(fn(Commission $row) => $row->customer->displayName);
        $commissionGrid->addColumnText('status', 'commission.commission.status')
            ->setTemplate(__DIR__ . '/commissionGridStatusCell.latte')
            ->setFilterMultiSelect(TranslatorUtils::mapTranslate(
                CommissionWorkflow::getStatusesLocalized(),
                $this->translator
            ));

        $commissionGrid->addColumnDateTime('acceptedOn', 'commission.commission.acceptedOn')
            ->setSortable();
        $commissionGrid->addColumnDateTime('targetDelivery', 'commission.commission.targetDelivery')
            ->setSortable();
        $commissionGrid->addColumnDateTime('archivedOn', 'commission.commission.archivedOn')
            ->setFilterSelect(TranslatorUtils::mapTranslate([
                'any' => 'generic.any',
                'active' => 'commission.commission.status.active',
                'archived' => 'commission.commission.status.archived',
            ], $this->translator))
            ->setCondition(function (Fluent $dataSource, $conditionValue) {
                $conditions = [];
                switch ($conditionValue) {
                    case 'active':
                        $conditions['archivedOn'] = null;
                        break;
                    case 'archived':
                        $conditions['!archivedOn'] = null;
                        break;
                }
                $this->queryFilter->apply($dataSource, $conditions, Commission::class);
            });

        return $commissionGrid;
    }
}
