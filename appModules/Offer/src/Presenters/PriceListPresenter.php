<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Presenters;

use Nette\Application\UI\Multiplier;
use PAF\Common\BasePresenter;
use PAF\Modules\OfferModule\Components\PriceList\OfferGroupControl;
use PAF\Modules\OfferModule\Model\ACL\PriceList;
use PAF\Modules\OfferModule\Model\Entity\Offer;
use PAF\Modules\OfferModule\Model\Service\OffersService;

class PriceListPresenter extends BasePresenter
{
    /** @inject */
    public OffersService $offersService;

    /** @var Offer[][] */
    private array $offers;

    public function actionDefault()
    {
        $this->offers = $this->offersService->getOffersByType();
    }

    public function renderDefault()
    {
        $this->template->canEditPriceList = $this->user->isAllowed(PriceList::OWN);
        $this->template->offers = $this->offers;
    }

    protected function createComponentOfferGroup()
    {
        return new Multiplier(function (string $groupName) {
            $offers = $this->offers[$groupName] ?? [];
            $offerGroupControl = $this->context->createInstance(OfferGroupControl::class, [
                'offerType' => $groupName,
                'offers' => $offers,
            ]);
            if ($this->user->isAllowed(PriceList::OWN)) {
                $offerGroupControl->setEditLink($this->link(':Offer:Offer:createOffer', ['type' => $groupName]));
            }
            return $offerGroupControl;
        });
    }
}
