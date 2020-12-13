<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionsGrid;

use Nette\Application\UI\ITemplateFactory;
use Nette\Localization\ITranslator;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Common\Localization\TranslatorUtils;
use Ublaboo\DataGrid\DataGrid;

class CommissionsGridFactory
{
    private ITranslator $translator;
    private ITemplateFactory $templateFactory;

    public function __construct(ITranslator $translator, ITemplateFactory $templateFactory)
    {
        $this->translator = $translator;
        $this->templateFactory = $templateFactory;
    }

    public function create()
    {
        $commissionGrid = new DataGrid();

        $commissionGrid->setTranslator($this->translator);

        $commissionGrid->addColumnText('fursuit', 'paf.entity.fursuit')
            ->setRenderer(fn (Commission $row) => $row->specification->characterName);
        $commissionGrid->addColumnText('customer', 'commission.customer')
            ->setRenderer(fn (Commission $row) => $row->customer->displayName);
        $commissionGrid->addColumnText('status', 'commission.commission.status')
            ->setRenderer(function (Commission $commission) {
                $template = $this->templateFactory->createTemplate();
                $template->commission = $commission;

                $template->setFile(__DIR__ .'/commissionGridStatusCell.latte');
                return $template;
            })
            ->setTemplateEscaping(false)
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
