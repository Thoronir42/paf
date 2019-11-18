<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\Utils\Strings;

class HtmlClassFilter extends BaseFilter
{
    public function useFilter(...$args): string
    {
        return Strings::webalize($args[0]);
    }
}
