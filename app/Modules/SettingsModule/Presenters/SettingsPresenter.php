<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Presenters;

use PAF\Common\BasePresenter;
use PAF\Modules\CommonModule\Presenters\Traits\DashboardComponent;
use SeStep\GeneralSettings\Model\INode;

final class SettingsPresenter extends BasePresenter
{
    use DashboardComponent;

    public function startup()
    {
        parent::startup();
    }

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
