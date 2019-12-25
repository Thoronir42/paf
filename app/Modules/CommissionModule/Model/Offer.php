<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

class Offer
{
    public const TYPE_PRODUCT = 'product';
    public const TYPE_EXTRAS = 'extras';
    public const TYPE_SERVICE = 'service';

    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var string|float */
    private $price;
    /** @var string */
    private $preview;
    /** @var Feature[] */
    private $features;

    public function __construct(string $name, string $type, $price, array $features = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->features = $features;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function setPreview(?string $preview): void
    {
        $this->preview = $preview;
    }
}
