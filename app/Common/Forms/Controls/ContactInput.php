<?php declare(strict_types=1);

namespace PAF\Common\Forms\Controls;

use Nette;
use Nette\Forms\Controls\TextInput;
use PAF\Modules\CommonModule\Components\ContactControl\ContactControl;
use PAF\Modules\CommonModule\Model\Contact;
use PAF\Modules\CommonModule\Services\ContactDefinitions;

class ContactInput extends TextInput
{
    /** @var Contact */
    protected $value;

    /** @var ContactDefinitions */
    private $definitions;

    public function __construct(ContactDefinitions $definitions, string $label = null)
    {
        parent::__construct($label);
        $this->definitions = $definitions;
    }

    public function getContactType(): ?string
    {
        return $this->value ? $this->value->type : null;
    }

    public function setContactType(string $type): self
    {
        if (!$this->value->isEmpty() && $this->value->type != $type) {
            throw new Nette\InvalidStateException("Can not set type to $type, value is already present");
        }

        $this->value->type = $type;

        return $this;
    }

    public function getValue()
    {
        return parent::getValue();
    }

    public function setValue($value)
    {
        if ($value === null) {
            $value = new Contact();
            $value->type = $this->value ? $this->value->type : null;
        }

        if (!$value instanceof Contact) {
            throw new \InvalidArgumentException("Value must be instance of " .
                Contact::class . ', got' . gettype($value));
        }

        $this->value = $value;
        $this->rawValue = $value->value ?: '';

        return $this;
    }

    public function getControl(): Nette\Utils\Html
    {
        $input = parent::getControl();
        $input->class[] = 'form-control';

        $wrapper = Nette\Utils\Html::el('div', [
            'class' => 'input-group',
        ]);
        $wrapper->addHtml($input);

        $addOn = $this->getAddOn();
        if ($addOn) {
            $append = Nette\Utils\Html::el('div', [
                'class' => 'input-group-append',
            ]);

            $append->addHtml($addOn);
            $wrapper->addHtml($append);
        }

        return $wrapper;
    }

    private function getAddOn(): ?Nette\Utils\Html
    {
        bdump($this->getContactType());
        if (!$this->value->isEmpty()) {
            $link = ContactControl::getIconHtml($this->definitions, $this->value);
            $link->class = 'input-group-text';

            return $link;
        } elseif ($this->getContactType()) {
            $span = ContactControl::getIconHtml($this->definitions, $this->value);
            $span->setName('span');
            $span->class = 'input-group-text';

            return $span;
        }

        return null;
    }
}
