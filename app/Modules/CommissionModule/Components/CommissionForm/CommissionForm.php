<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionForm;

use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;
use PAF\Common\Forms\FormFactory;
use PAF\Common\Forms\FormWrapperControl;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use PAF\Common\Lean\GenericEntityForm;
use PAF\Modules\CommissionModule\Facade\ProductService;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\DirectoryModule\Model\Contact;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\DirectoryModule\Services\ContactDefinitions;
use stdClass;

/**
 * @method ITemplate|stdClass createTemplate
 * @method onSave(Commission $commission, Form $form)
 */
class CommissionForm extends FormWrapperControl
{
    /** @var callable[]  function (Form $form, ArrayHash $result); Occurs when form successfully validates input. */
    public array $onSave = [];

    private ContactDefinitions $contactDefinitions;
    private ProductService $productService;

    public function __construct(
        FormFactory $formFactory,
        ITranslator $translator,
        ContactDefinitions $contactDefinitions,
        ProductService $productService
    ) {
        parent::__construct($formFactory, $translator);
        $this->contactDefinitions = $contactDefinitions;
        $this->productService = $productService;
        $this['contact'] = new Container();
    }

    private ?Commission $commission = null;

    public function setEntity(Commission $commission)
    {
        $this->commission = $commission;

        $form = $this->form();
        $form->setDefaults([
            'specification' => [
                'type' => $commission->specification->type,
                'characterName' => $commission->specification->characterName,
                'characterDescription' => $commission->specification->characterDescription,
            ],
            'contact' => $this->getContactDefaultData($commission->customer),
            'status' => $commission->status,
            'targetDelivery' => $commission->targetDelivery,
        ]);
    }

    public function render()
    {
        $template = $this->createTemplate();

        $template->setFile(__DIR__ . '/commissionForm.latte');
        $template->render();
    }

    public function createComponentForm()
    {
        /** @var GenericEntityForm $form */
        $form = $this->factory->create();

        $form->addGroup('fursuit-specification');
        $fursuit = $form->addContainer('specification');

        $fursuit->addText('characterName', 'paf.fursuit.name');
        $fursuit->addSelect('type', 'paf.fursuit.type', $this->productService->getTypes());
        $fursuit->addTextarea('characterDescription', 'paf.fursuit.description');

        $form->addGroup('contact');
        $contact = $form->addContainer('contact');

        $contact->addText('name', 'paf.contact.name');
        $contact->addContact('telegram', $this->contactDefinitions, 'paf.contact.telegram');
        $contact->addContact('email', $this->contactDefinitions, 'paf.contact.email');
        $contact->addContact('telephone', $this->contactDefinitions, 'paf.contact.phone')
            ->setContactType(Contact::TYPE_TELEPHONE);

        $this->setContainerDisabled($fursuit, true);
        $this->setContainerDisabled($contact, true);

        $form->addDate('targetDelivery', 'commission.commission.targetDelivery');

        $form->addSubmit('submit', 'generic.update');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }


    public function processForm(Form $form, $values)
    {
        $commission = $this->commission;

        if (count($values->specification)) {
            $specification = $this->commission->specification;
            $specification->type = $values->specification->type;
            $specification->characterDescription = $values->fursuit->characterDescription;
        }

        $targetDelivery = $values->targetDelivery;

        if ($targetDelivery != $this->commission->targetDelivery) {
            $commission->targetDelivery = $targetDelivery instanceof \DateTime ? $targetDelivery : null;
        }


        $this->onSave($commission, $form);
    }

    private function getContactDefaultData(Person $customer): array
    {
        $data = [
            'name' => $customer->displayName,
        ];

        foreach ($customer->contact as $contact) {
            $data[$contact->type] = $contact;
        }

        return $data;
    }

    private function setContainerDisabled(Container $container, $disabled = false)
    {
        /** @var BaseControl $component */
        foreach ($container->getComponents() as $component) {
            $component->setDisabled($disabled);
        }
    }
}
