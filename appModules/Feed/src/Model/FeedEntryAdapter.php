<?php declare(strict_types=1);

namespace PAF\Modules\Feed\Model;

use DateTime;

final class FeedEntryAdapter implements FeedEntry
{
    private string $id;
    private string $type;
    private DateTime $instant;

    private $source;

    /**
     * @param string $id
     * @param string $type
     * @param DateTime $instant
     * @param mixed $source
     */
    public function __construct(string $id, string $type, DateTime $instant, $source)
    {
        $this->id = $id;
        $this->type = $type;
        $this->instant = $instant;
        $this->source = $source;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInstant(): DateTime
    {
        return $this->instant;
    }

    public function getSource()
    {
        return $this->source;
    }
}
