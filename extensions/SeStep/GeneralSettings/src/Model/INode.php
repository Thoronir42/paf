<?php declare(strict_types=1);

namespace SeStep\GeneralSettings\Model;

interface INode
{
    const DOMAIN_DELIMITER = '.';

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string;

    public function getType(): string;

    public function getCaption(): ?string;
}
