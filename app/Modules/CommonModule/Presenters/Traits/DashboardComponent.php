<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters\Traits;

use Nette\DI\Container;
use PAF\Modules\CommonModule\Services\DashboardService;
use SeStep\NetteBootstrap\Controls\Menu\MenuControl;

/**
 * Trait DashboardComponent for presenters that are part of Dashboard
 *
 * @property-read Container $context
 * @property-read \stdClass $template
 */
trait DashboardComponent
{
    /** @var DashboardService @inject */
    public $dashboardService;

    public function createComponentDashboardNavigation()
    {
        return $this->context->getService('paf.dashboardNavigation');
    }

    public function beforeRender()
    {
        $this->template->dashboardTemplate = __DIR__ . '/../../templates/Dashboard/_dashboard.latte';
        /** @var MenuControl $navigation */
        $navigation = $this['dashboardNavigation'];

        $dashboardStats = $this->dashboardService->getStats();
        if ($dashboardStats['quotes'] > 0) {
            $navigation['quoteList']->getItem()->addLabel((string)$dashboardStats['quotes'], 'info');
        }

        if ($dashboardStats['commissions'] > 0) {
            $navigation['commissionList']->getItem()->addLabel((string)$dashboardStats['commissions'], 'info');
        }
    }
}
