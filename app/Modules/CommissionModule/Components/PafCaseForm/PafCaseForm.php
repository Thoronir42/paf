<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\PafCaseForm;

use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;
use PAF\Common\Forms\FormFactory;
use PAF\Common\Forms\FormWrapperControl;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use PAF\Modules\CommissionModule\Facade\ProductService;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommonModule\Model\Contact;
use PAF\Modules\CommonModule\Model\Person;
use PAF\Modules\CommonModule\Services\ContactDefinitions;
use stdClass;

/**
 * Class PafCaseForm
 * @package PAF\Modules\Admin\Controls\CaseControl
 *
 * @method ITemplate|stdClass createTemplate
 * @method onSave(PafCase $case, Form $form)
 */
class PafCaseForm extends FormWrapperControl
{
    /** @var callable[]  function (Form $form, ArrayHash $result); Occurs when form successfully validates input. */
    public $onSave = [];

    /** @var ContactDefinitions */
    private $contactDefinitions;
    /**
     * @var ProductService
     */
    private $productService;

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

    /** @var PafCase */
    private $case;

    public function setEntity(PafCase $case)
    {
        $this->case = $case;

        $form = $this->form();
        $form->setDefaults([
            'specification' => [
                'type' => $case->specification->type,
                'characterName' => $case->specification->characterName,
                'characterDescription' => $case->specification->characterDescription,
            ],
            'contact' => $this->getContactDefaultData($case->customer),
            'status' => $case->status,
            'targetDelivery' => $case->targetDelivery,
        ]);
    }

    public function render()
    {
        $template = $this->createTemplate();
        $template->case = $this->case;

        $template->setFile(__DIR__ . '/pafCaseForm.latte');
        $template->render();
    }

    public function createComponentForm()
    {
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

        $form->addDate('targetDelivery', 'commission.case.targetDelivery');

        $form->addSubmit('submit', 'generic.update');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }


    public function processForm(Form $form, $values)
    {
        $case = $this->case;

        if (count($values->specification)) {
            $specification = $this->case->specification;
            $specification->type = $values->specification->type;
            $specification->characterDescription = $values->fursuit->characterDescription;
        }

        $targetDelivery = $values->targetDelivery;

        if ($targetDelivery != $this->case->targetDelivery) {
            $case->targetDelivery = $targetDelivery instanceof \DateTime ? $targetDelivery : null;
        }


        $this->onSave($case, $form);
    }

    private function getContactDefaultData(Person $customer)
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
