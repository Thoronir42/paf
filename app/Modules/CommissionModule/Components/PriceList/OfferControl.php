<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\PriceList;

use Nette\Application\UI;
use PAF\Modules\CommissionModule\Model\Offer;

class OfferControl extends UI\Control
{
    /** @var Offer */
    private $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function render()
    {
        $this->template->offer = $this->offer;
        $this->template->setFile(__DIR__ . '/offerControl.latte');
        $this->template->render();
    }
}
