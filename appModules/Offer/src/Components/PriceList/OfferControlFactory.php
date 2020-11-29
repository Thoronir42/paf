<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Components\PriceList;

use Nette\Security\User;
use PAF\Modules\DirectoryModule\Services\PersonService;
use PAF\Modules\OfferModule\Model\Entity\Offer;
use SeStep\Typeful\Entity\EntityDescriptor;

class OfferControlFactory
{
    private PersonService $personService;
    private User $user;
    private EntityDescriptor $entityDescriptor;

    public function __construct(PersonService $personService, User $user, EntityDescriptor $entityDescriptor)
    {
        $this->personService = $personService;
        $this->user = $user;
        $this->entityDescriptor = $entityDescriptor;
    }

    public function create(Offer $offer): OfferControl
    {
        $control = new OfferControl($offer, $this->entityDescriptor);
        $control->injectAppUser($this->personService, $this->user);

        return $control;
    }
}
