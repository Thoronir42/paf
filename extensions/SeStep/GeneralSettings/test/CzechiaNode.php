<?php declare(strict_types=1);

namespace Test\SeStep\GeneralSettings;

use SeStep\GeneralSettings\Options\INode;

class CzechiaNode implements INode
{

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string
    {
        return 'earth.continents.europe.czechia';
    }

    public function getType(): string
    {
        return 'container';
    }

    public function getCaption(): ?string
    {
        return 'The silly country';
    }
}
