<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Nette\InvalidArgumentException;
use PAF\Common\BasePresenter;
use PAF\Modules\CommonModule\Presenters\Traits\DashboardComponent;
use PAF\Modules\DirectoryModule\Services\HasAppUser;
use PAF\Modules\OfferModule\Model\Service\OffersService;
use SeStep\NetteTypeful\Components\EntityGridFactory;
use SeStep\NetteTypeful\Forms\EntityFormFactory;
use Ublaboo\DataGrid\DataGrid;

class OfferPresenter extends BasePresenter
{
    const OFFER_FIELDS = [
        'slug',
        'name',
        'type',
        'description',
        'previewImagePath',
        'standardPrice',
    ];

    use DashboardComponent;
    use HasAppUser;

    /** @inject */
    public EntityFormFactory $entityFormFactory;
    /** @inject */
    public EntityGridFactory $gridFactory;
    /** @inject */
    public OffersService $offersService;

    public function checkRequirements($element): void
    {
        if (!$this->dirPerson) {
            throw new BadRequestException("Current user is not a supplier.", IResponse::S401_UNAUTHORIZED);
        }
        parent::checkRequirements($element);
    }

    public function actionIndex()
    {
        /** @var DataGrid $offersGrid */
        $offersGrid = $this['offersGrid'];
        $offersGrid->setDataSource($this->offersService->getOfferDataSource(['supplier' => $this->dirPerson]));
        $offersGrid->addAction('editOffer', 'edit');

    }

    /// Action

    public function actionCreateOffer(string $type = null)
    {
        $this['offerForm'] = $offerForm = $this->entityFormFactory->create(
            'pafOffer.offer',
            true,
            self::OFFER_FIELDS,
        );

        try {
            $offerForm->setDefaults(['type' => $type]);
        } catch (InvalidArgumentException $ex) {
            $this->redirect('this', ['type' => null]);
        }


        $offerForm->onSuccess[] = function ($form, $values) {
            $this->offersService->createNewOffer((array)$values, $this->dirPerson);
            $this->flashMessage('offerCreated');
            $this->redirect('index');
        };
    }

    public function actionEditOffer(string $id)
    {
        $offer = $this->offersService->findOffer($id);
        if (!$offer) {
            throw new BadRequestException("", IResponse::S404_NOT_FOUND);
        }

        $this['offerForm'] = $offerForm = $this->entityFormFactory->create(
            'pafOffer.offer',
            false,
            self::OFFER_FIELDS,
        );

        $offerForm->onSuccess[] = function ($form, $values) use ($offer) {
            $updated = $this->offersService->updateOffer($offer, (array)$values);
            if ($updated) {
                $this->flashMessage('offer.messages.offerUpdated');
            }
            $this->redirect('this');
        };

        $offerForm->setDefaults($offer);
        $this->template->offer = $offer;
    }

    /// Render

    public function renderCreateOffer()
    {
        $this->view = 'editOffer';
    }
    
    public function createComponentOffersGrid()
    {
        return $this->gridFactory->create('pafOffer.offer', ['type', 'name', 'standardPrice']);
    }
}
