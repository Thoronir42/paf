<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\templates\PriceList;

class PriceListDefaultTemplate
{
    public \Nette\Security\User $user;
    public string $baseUrl;
    public string $basePath;
    public array $flashes;
    public \PAF\Modules\OfferModule\Presenters\PriceListPresenter $control;
    public \PAF\Modules\OfferModule\Presenters\PriceListPresenter $presenter;
    public string $lang;
    public string $appName;
    public string $title;
    public bool $canEditPriceList;
    public array $offers;
}
