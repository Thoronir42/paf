<?php

namespace App\Modules\Front\Presenters;


use App\Common\Controls\Forms\QuoteForm\IQuoteFormFactory;
use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\Fursuits;
use App\Common\Services\Doctrine\PafEntities;
use App\Common\Services\Doctrine\Quotes;
use App\Common\Services\Storage\PafImageStorage;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use SeStep\FileAttachable\Service\Files;

class QuotesPresenter extends FrontPresenter
{
    /** @var IQuoteFormFactory @inject */
    public $quoteFormFactory;

    /** @var Quotes @inject */
    public $quotes;

    /** @var PafEntities */
    public $pafEntities;

    /** @var Fursuits @inject */
    public $fursuits;

    /** @var PafImageStorage @inject */
    public $pafImages;

    /** @var Files @inject */
    public $files;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {
        $this->template->enableQuotes = $this->options->getValue('paf.quotes.enable_quotes');

        /*$contact = (new Contact("Karel"))
            ->setEmail("ka@ta.lan")
            ->setTelegram("katalanec");
        $fursuitSpecification = (new FursuitSpecification('Kaja'))
            ->setType(Fursuit::TYPE_PARTIAL)
            ->setCharacterDescription("Kaja to je vocas. Krasny gradientni vocas.");
        $quote = new Quote($contact, $fursuitSpecification);
        dump($quote->toArray()); exit;
        $this['quoteForm']->setEntity($quote);*/

    }

    public function createComponentQuoteForm()
    {
        $form = $this->quoteFormFactory->create();

        $form->onSave[] = function (Quote $quote, Form $form, $references) {
            $entity = $this->pafEntities->findByName($quote->getSlug());
            if ($entity) {
                $entityProgress = $this->translator->translate('paf.entity.' . $entity->getMaxProgress());
                $errorVariables = [
                    'name'     => $quote->getSlug(),
                    'progress' => $entityProgress,
                ];
                $errorMessage = $this->translator->translate('paf.entity.already-exists', $errorVariables);
                $form['fursuit']['name']->addError($errorMessage);
            }

            $refs = $this->files->createThread(true);
            $quote->setReferences($refs);

            /**@var FileUpload[] $references */
            foreach ($references as $file) {
                $fileEntity = $this->pafImages->saveQuoteReference($quote, $file, $quote->getSlug());
                $refs->addFile($fileEntity);
            }

            $this->pafEntities->createQuote($quote);
            $this->flashTranslate('paf.quote.created');
        };

        return $form;
    }
}
