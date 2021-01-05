<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\Localization\ITranslator;

final class YesNoFilter extends BaseFilter
{
    private ?ITranslator $translator;

    public function __construct(ITranslator $translator = null)
    {
        $this->translator = $translator;
    }

    public function useFilter(...$args): string
    {
        $value = !$args || !is_array($args) || sizeof($args) < 1 || !$args[0];
        $strVal = $value ? "Yes" : "No";

        if ($this->translator) {
            return $this->translator->translate($strVal);
        }

        return $strVal;
    }
}
