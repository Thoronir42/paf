<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

class ProductService
{
    public function getTypes()
    {
        return [
            'partial' => 'commission.productType.partial',
            'halfSuit' => 'commission.productType.halfSuit',
            'fullSuit' => 'commission.productType.fullSuit',
        ];
    }
}
