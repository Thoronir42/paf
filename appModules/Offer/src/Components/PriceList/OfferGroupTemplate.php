<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Components\PriceList;

use PAF\Modules\OfferModule\Model\Entity\Offer;

class OfferGroupTemplate
{
    public \Nette\Security\User $user;
    public string $baseUrl;
    public string $basePath;
    public array $flashes;
    public \PAF\Modules\OfferModule\Components\PriceList\OfferGroupControl $control;
    public \PAF\Modules\OfferModule\Presenters\PriceListPresenter $presenter;
    public string $class;
    public string $offerType;
    /** @var Offer[] */
    public array $offers;
    public string $editLink;
}
