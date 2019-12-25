<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters\Traits;

use Nette\DI\Container;
use PAF\Modules\CommissionModule\Facade\Commissions;
use SeStep\NetteBootstrap\Controls\Menu\MenuControl;

/**
 * Trait DashboardComponent for presenters that are part of Dashboard
 *
 * @property-read Container $context
 * @property-read \stdClass $template
 */
trait DashboardComponent
{
    /** @var Commissions @inject */
    public $commissions;

    public function createComponentDashboardNavigation()
    {
        return $this->context->getService('paf.dashboardNavigation');
    }

    public function beforeRender()
    {
        $this->template->dashboardTemplate = __DIR__ . '/../../templates/Dashboard/_dashboard.latte';
        /** @var MenuControl $navigation */
        $navigation = $this['dashboardNavigation'];

        $quotes = $this->commissions->countUnresolvedQuotes();
        if ($quotes > 0) {
            $navigation['quoteList']->getItem()->addLabel((string)$quotes, 'info');
        }

        $cases = $this->commissions->countUnresolvedCases();
        if ($cases > 0) {
            $navigation['caseList']->getItem()->addLabel((string)$cases, 'info');
        }
    }
}
