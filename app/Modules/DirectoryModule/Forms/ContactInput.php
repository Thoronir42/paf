<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Forms;

use Nette;
use Nette\Forms\Controls\TextInput;
use PAF\Modules\DirectoryModule\Components\ContactControl\ContactControl;
use PAF\Modules\DirectoryModule\Model\Contact;
use PAF\Modules\DirectoryModule\Services\ContactDefinitions;
use SeStep\NetteBootstrap as NBS;

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

        $inputGroup = new NBS\InputGroup($input);

        $addOn = $this->getAddOn();
        if ($addOn) {
            $inputGroup->append($addOn);
        }

        return $inputGroup;
    }

    private function getAddOn(): ?Nette\Utils\Html
    {
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
