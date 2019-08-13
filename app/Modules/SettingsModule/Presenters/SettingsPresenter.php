<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Presenters;

use Exception;
use Nette\Application\AbortException;
use PAF\Common\BasePresenter;
use PAF\Modules\SettingsModule\Components\SettingsControl\OptionNodeControl;
use PAF\Modules\SettingsModule\InlineOption\SettingsOptionAccessor;
use SeStep\GeneralSettings\Model\INode;

final class SettingsPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();

//        $this->validateAuthorization('admin-settings', Authorizator::READ, ':Common:Homepage:');
    }

    public function actionDefault(string $fqn = 'paf')
    {
        $this->template->fqnComponent = str_replace(INode::DOMAIN_DELIMITER, '-', $fqn);

        $this['settings'] = $this->createComponentOption();

    }
}
