<?php declare(strict_types=1);

namespace PAF\Modules\PortfolioModule;


use PAF\Modules\PortfolioModule\Model\Fursuit;

class Localization
{
    public static function getFursuitTypes()
    {
        $types = [];
        foreach (Fursuit::getTypes() as $type) {
            $types[$type] = "paf.fursuit.types.$type";
        }

        return $types;
    }

}