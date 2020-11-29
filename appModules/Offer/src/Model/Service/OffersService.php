<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Model\Service;

use PAF\Common\Lean\LeanMapperDataSource;
use PAF\Common\Lean\LeanTypefulRepository;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\OfferModule\Model\Entity\Offer;

class OffersService
{
    private LeanTypefulRepository $typefulRepository;

    public function __construct(LeanTypefulRepository $typefulRepository)
    {
        $this->typefulRepository = $typefulRepository;
    }

    public function getOfferDataSource(array $conditions = null): LeanMapperDataSource
    {
        return $this->typefulRepository->getRepository()->getEntityDataSource($conditions);
    }

    /**
     * @return Offer[][]
     */
    public function getOffersByType()
    {
        $offers = $this->typefulRepository->getRepository()->findAll();
        $groups = [];
        /** @var Offer $offer */
        foreach ($offers as $id => $offer) {
            if (!isset($offers[$offer->type])) {
                $groups[$offer->type] = [];
            }

            $groups[$offer->type][$id] = $offer;
        }

        return $groups;
    }

    public function createNewOffer(array $values, Person $supplier): Offer
    {
        $values['supplier'] = $supplier;
        /** @var Offer $offer */
        $offer = $this->typefulRepository->createNewFromTypefulData($values);
        return $offer;
    }

    public function findOffer(string $id): ?Offer
    {
        return $this->typefulRepository->getRepository()->find($id);
    }

    public function updateOffer(Offer $offer, array $values)
    {
        $this->typefulRepository->updateWithTypefulData($offer, $values);
    }
}
