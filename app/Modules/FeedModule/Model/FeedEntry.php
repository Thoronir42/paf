<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Model;

use DateTime;

interface FeedEntry
{
    public function getId(): string;

    public function getType(): string;

    public function getInstant(): DateTime;

    public function getSource();
}
