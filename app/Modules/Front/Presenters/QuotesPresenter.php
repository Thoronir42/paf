<?php

namespace App\Modules\Front\Presenters;


use App\Common\Controls\Forms\QuoteForm\IQuoteFormFactory;
use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\Fursuits;
use App\Common\Services\Doctrine\Quotes;
use Nette\Forms\Form;

class QuotesPresenter extends FrontPresenter
{
    /** @var IQuoteFormFactory @inject */
    public $form_factory;

    /** @var Quotes @inject */
    public $quotes;

    /** @var Fursuits @inject */
    public $fursuits;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {
        $this->template->enableQuotes = $this->settings->getValue('paf.quotes.enable_quotes');

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
        $form = $this->form_factory->create();

        $form->onSave[] = function (Quote $quote, Form $form) {
            if ($this->quotes->slugExists($quote->getSlug())) {
                $form['name']->addError("Quote with this name already exists");
                return;
            }
            if ($this->fursuits->slugExists($quote->getSlug())) {
                $form['name']->addError("Fursuit with this name already exists");
                return;
            }

//            $this->quotes->save($quote);
            $this->flashMessage('todo: quote-saved'); // todo
//            dump($quote); exit;
        };

        return $form;
    }
}
