<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\InvalidArgumentException;

final class ContactUrlFilter extends BaseFilter
{
    /** @var string[] */
    private $formats;

    /** @param string[] $formats */
    public function __construct(array $formats)
    {
        $this->formats = $formats;
    }


    public function useFilter(...$args): string
    {
        if (!isset($args[1])) {
            throw new InvalidArgumentException("Filter needs contact type argument");
        }
        if (!isset($this->formats[$args[1]])) {
            throw new InvalidArgumentException("Contact filter '$args[1]' not recognized");
        }

        return sprintf($this->formats[$args[1]], $args[0]);
    }
}
