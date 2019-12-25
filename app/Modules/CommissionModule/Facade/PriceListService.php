<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use PAF\Modules\CommissionModule\Model\Feature;
use PAF\Modules\CommissionModule\Model\Offer;

class PriceListService
{
    /** @var Offer[] */
    private $offers;

    /** @var Feature[] */
    private $features;

    public function __construct(array $data)
    {
        $this->features = self::parseFeatures($data['features']);
        $this->offers = self::parseOffers($data['offers'], $this->features);
    }

    /**
     * @return Offer[][]
     */
    public function getOffersByType(): array
    {
        $result = [];
        foreach ($this->offers as $name => $offer) {
            $result[$offer->getType()][$name] = $offer;
        }

        return $result;
    }

    /**
     * @param array $featuresData
     *
     * @return Feature[]
     */
    private static function parseFeatures(array $featuresData): array
    {
        $features = [];
        foreach ($featuresData as $featureData) {
            $features[$featureData] = new Feature("priceList.feature.$featureData");
        }

        return $features;
    }

    private static function parseOffers(array $offersData, array $features)
    {
        $offers = [];
        foreach ($offersData as $name => $offerData) {
            if (isset($offerData['features'])) {
                $offerFeatures = array_intersect_key($features, array_flip($offerData['features']));
            } else {
                $offerFeatures = [];
            }

            $offer = new Offer($offerData['name'], $offerData['type'], $offerData['price'], $offerFeatures);
            $offers[$name] = $offer;
        }

        return $offers;
    }
}
