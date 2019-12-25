<?php declare(strict_types=1);

namespace PAF\Common\Latte;

class PriceFilter extends BaseFilter
{
    /** @var string */
    private $currencyFormat;

    public function __construct(string $currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;
    }

    public function useFilter(...$args): string
    {
        $price = $args[0];
        $decimals = $args[1] ?? 0;

        return sprintf($this->currencyFormat, number_format($price, $decimals));
    }
}
