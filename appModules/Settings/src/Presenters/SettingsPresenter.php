<?php declare(strict_types=1);

namespace PAF\Modules\Settings\Presenters;

use PAF\Common\BasePresenter;
use PAF\Modules\CommonModule\Presenters\Traits\DashboardComponent;
use SeStep\GeneralSettings\Model\INode;

class SettingsPresenter extends BasePresenter
{
    use DashboardComponent;

    /**
     * @param string $fqn
     *
     * @authorize admin-settings
     */
    public function actionDefault(string $fqn = '')
    {
        $this->template->fqnComponent = str_replace(INode::DOMAIN_DELIMITER, '-', $fqn);

        $this['settings'] = $this->createComponentOption();
    }
}
