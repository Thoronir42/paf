<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\UploadControl;
use PAF\Common\Forms\Controls\DateInput;
use PAF\Modules\DirectoryModule\Forms\ContactInput;
use PAF\Modules\DirectoryModule\Services\ContactDefinitions;
use SeStep\NetteBootstrap\Controls as NBSC;

trait FormCustomControls
{
    public function addDate(string $name, string $label = null): Controls\DateInput
    {
        return $this[$name] = new Controls\DateInput(DateInput::FORMAT_DATE, $label);
    }

    public function addDateTime(string $name, string $label = null): Controls\DateInput
    {
        return $this[$name] = new Controls\DateInput(DateInput::FORMAT_DATETIME, $label);
    }

    public function addContact(
        string $name,
        ContactDefinitions $definitions,
        string $label = null
    ): ContactInput {
        return $this[$name] = new ContactInput($definitions, $label);
    }

    public function addUpload(string $name, $label = null): UploadControl
    {
        return $this[$name] = new NBSC\BootstrapUploadControl($label, false);
    }

    public function addMultiUpload(string $name, $label = null): UploadControl
    {
        return $this[$name] = new NBSC\BootstrapUploadControl($label, true);
    }

    public function addCheckbox(string $name, $caption = null): Checkbox
    {
        return $this[$name] = new NBSC\BootstrapCheckboxControl($caption);
    }

    /**
     * @param string $name
     * @return Container
     */
    public function addContainer($name): \Nette\Forms\Container
    {
        $control = new Container();
        $control->currentGroup = $this->currentGroup;

        return $this[$name] = $control;
    }
}
