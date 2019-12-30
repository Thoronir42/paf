<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use PAF\Common\BasePresenter;
use PAF\Modules\CommissionModule\Components\PriceList\OfferControl;
use PAF\Modules\CommissionModule\Facade\PriceListService;
use PAF\Modules\CommissionModule\Model\Offer;

class PriceListPresenter extends BasePresenter
{
    /** @var PriceListService @inject */
    public $priceList;

    /** @var Offer[][] */
    private $offers;

    public function actionDefault()
    {
        $this->offers = $this->priceList->getOffersByType();
    }

    protected function createComponent(string $offerGroup): ?IComponent
    {
        if (!array_key_exists($offerGroup, $this->offers)) {
            return parent::createComponent($offerGroup);
        }

        return new Multiplier(function ($offer) use ($offerGroup) {
            return new OfferControl($this->offers[$offerGroup][$offer]);
        });
    }
}
