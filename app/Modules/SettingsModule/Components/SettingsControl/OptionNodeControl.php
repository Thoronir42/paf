<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;


use Nette\Application\UI;
use SeStep\GeneralSettings\Options\INode;

/**
 * Class OptionControl
 *
 * @package SeStep\SettingsControl
 *
 * @method onSetValue(string $name, $value)
 */
class OptionNodeControl extends UI\Control
{
    /** @var callable[] */
    public $onSetValue = [];

    /** @var INode */
    private $node;


    public function __construct(INode $node)
    {
        $this->node = $node;
    }

    public function render()
    {
        $el = new OptionElement($this->node);

        if ($this->node->hasValues()) {
            $el->data('value-pool', $this->link('values!'));
        }

        $el->data('url-set', $this->link('set!'));
        $el->data('value-name', $this->getUniqueId() . '-value');

        echo $el->render();
    }

    public function handleValues()
    {
        if ($this->node->hasValues()) {
            $this->presenter->sendJson($this->node->getValues());
        }
        $this->presenter->sendJson(['status' => 'error', 'message' => 'This option does not support values']);
    }

    public function handleSet()
    {
        $value = $this->getParameter('value');

        $this->onSetValue($this->node->getFQN(), $value);
    }


}
