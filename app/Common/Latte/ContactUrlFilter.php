<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\InvalidArgumentException;
use PAF\Modules\CommonModule\Services\ContactDefinitions;

final class ContactUrlFilter extends BaseFilter
{
    /** @var ContactDefinitions */
    private $contactDefinitions;

    public function __construct(ContactDefinitions $contactDefinitions)
    {
        $this->contactDefinitions = $contactDefinitions;
    }

    public function useFilter(...$args): string
    {
        if (!isset($args[1])) {
            throw new InvalidArgumentException("Filter needs contact type argument");
        }
        if (!isset($this->formats[$args[1]])) {
            throw new InvalidArgumentException("Contact filter '$args[1]' not recognized");
        }

        return $this->contactDefinitions->formatHref($args[1], $args[0]);
    }
}
