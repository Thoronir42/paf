<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Components\PriceList;

use Nette\Application\UI;
use Nette\Bridges\ApplicationLatte\Template;
use PAF\Modules\DirectoryModule\Services\HasAppUser;
use PAF\Modules\DirectoryModule\Services\PersonService;
use PAF\Modules\OfferModule\Model\Entity\Offer;
use SeStep\Typeful\Entity\EntityDescriptor;

/**
 * @property OfferControlTemplate|Template $template
 */
class OfferControl extends UI\Control
{
    use HasAppUser;

    private Offer $offer;
    private EntityDescriptor $descriptor;

    public function __construct(Offer $offer, EntityDescriptor $descriptor)
    {
        $this->offer = $offer;
        $this->descriptor = $descriptor;
    }

    public function render()
    {
        $this->template->offer = $this->offer;
        $this->template->canEdit = $this->offer->supplier->id === $this->dirPerson->id;
        $this->template->setFile(__DIR__ . '/offerControl.latte');
        $this->template->render();
    }

    public function getPreviewUrl(string $fileName)
    {
        $property = $this->descriptor->getProperty('previewImagePath');
        return $property->getOptions()['publicPath'] . '/' . $fileName;
    }
}
