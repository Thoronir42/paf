<?php declare(strict_types=1);

namespace PAF\Common\Localization;

use Nette\Localization\ITranslator;

class TranslatorUtils
{
    /**
     * Using provided `$translator`, translates each item in `$values`
     *
     * @param string[] $values
     * @param ITranslator $translator
     * @return string[]
     */
    public static function mapTranslate(array $values, ITranslator $translator): array
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[$key] = $translator->translate($value);
        }

        return $result;
    }
}
