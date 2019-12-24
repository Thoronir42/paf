<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters;

use PAF\Common\BasePresenter;

class DashboardPresenter extends BasePresenter
{
    public function createComponentDashboardNavigation()
    {
        return $this->context->getService('paf.dashboardNavigation');
    }

    public function beforeRender()
    {
        $this->template->dashboardTemplate = dirname(__DIR__) . '/templates/Dashboard/_dashboard.latte';
    }
}
