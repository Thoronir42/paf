<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Components\PriceList;

use Nette\Application\UI;
use PAF\Modules\DirectoryModule\Services\PersonService;
use PAF\Modules\OfferModule\Model\Entity\Offer;

class OfferGroupControl extends UI\Control
{
    private PersonService $personService;
    private OfferControlFactory $offerControlFactory;
    private string $offerType;
    /** @var Offer[] */
    private array $offers;

    private ?string $editLink = null;


    public function __construct(
        PersonService $personService,
        OfferControlFactory $offerControlFactory,
        string $offerType,
        array $offers
    ) {
        $this->personService = $personService;
        $this->offerControlFactory = $offerControlFactory;
        $this->offerType = $offerType;
        $this->offers = $offers;
    }

    public function setEditLink(?string $editLink): void
    {
        $this->editLink = $editLink;
    }

    public function render(string $class = null)
    {
        $this->template->class = $class;
        $this->template->offerType = $this->offerType;
        $this->template->offers = $this->offers;
        $this->template->editLink = $this->editLink;
        $this->template->render(__DIR__ . '/offerGroupControl.latte');
    }

    public function createComponent(string $name): OfferControl
    {
        return $this->offerControlFactory->create($this->offers[$name]);
    }
}
