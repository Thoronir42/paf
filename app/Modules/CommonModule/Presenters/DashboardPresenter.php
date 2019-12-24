<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters;

use PAF\Common\BasePresenter;
use PAF\Modules\CommissionModule\Facade\Commissions;
use SeStep\NetteBootstrap\Controls\Menu\MenuControl;

class DashboardPresenter extends BasePresenter
{
    /** @var Commissions @inject */
    public $commissions;

    public function createComponentDashboardNavigation()
    {
        return $this->context->getService('paf.dashboardNavigation');
    }

    public function beforeRender()
    {
        $this->template->dashboardTemplate = dirname(__DIR__) . '/templates/Dashboard/_dashboard.latte';
        /** @var MenuControl $navigation */
        $navigation = $this['dashboardNavigation'];

        $navigation['quoteList']->getItem()->addLabel((string)$this->commissions->countUnresolvedQuotes(), 'info');
        $navigation['caseList']->getItem()->addLabel((string)$this->commissions->countUnresolvedCases(), 'info');
    }
}
