<?php

namespace App\Utils\Migrations;


use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Entity\Fursuit;
use SeStep\Migrations\Base\InitializerModuleBase;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Options\IOptions;

class CoreInitializerModule extends InitializerModuleBase
{
    /** @var CoreEntityCreator */
    private $add;

    protected function setup()
    {
        $this->add = new CoreEntityCreator($this->provider, $this->output);
    }

    public function run()
    {
        $sections = $this->addSections();
        $this->addOptions($sections);
        $this->addUsers();
        $this->addQoutes();
    }

    private function addSections()
    {
        $sections = [
            'paf.quotes' => $this->add->section('paf.quotes', 'Quotes related settings'),
            'paf.priceList' => $this->add->section('paf.priceList', 'Price list'),
        ];

        return $sections;
    }

    /**
     * @param OptionsSection[] $sections
     */
    private function addOptions($sections = [])
    {
        $this->add->option(IOptions::TYPE_BOOL, 'Enable quotes', true, null, $sections['paf.quotes']);
        $this->add->option(IOptions::TYPE_STRING, 'Preffered species', '', null, $sections['paf.quotes']);

        $this->add->option(IOptions::TYPE_INT, 'Base suit price', 420, null, $sections['paf.priceList']);
        $this->add->option(IOptions::TYPE_INT, 'Extra feature', 50, null, $sections['paf.priceList']);
    }

    private function addUsers()
    {
        $this->add->user('Toanir', 'test');
        $this->add->user('Toust', 'test');
    }

    private function addQoutes()
    {
        $contact = (new Contact("T-boy"))->setEmail("t.boi42@@gmail.com")->setTelegram("t.boi");
        $fursuitSpec = (new FursuitSpecification("Thumb", Fursuit::TYPE_PARTIAL))
            ->setCharacterDescription("Big teethy\nHeary heary\nFiery occulery");
        $this->add->quote($contact, $fursuitSpec);

        $fursuitSpec = (new FursuitSpecification("Pinky", Fursuit::TYPE_PARTIAL))
            ->setCharacterDescription("Big teethy\nHeary heary\nFiery occulery");
        $this->add->quote($contact, $fursuitSpec);

        $fursuitSpec = (new FursuitSpecification("Collar", Fursuit::TYPE_HALF_SUIT))
            ->setCharacterDescription("Big teethy\nHeary heary\nFiery occulery");
        $this->add->quote($contact, $fursuitSpec);

        $fursuitSpec = (new FursuitSpecification("Jazzy", Fursuit::TYPE_FULL_SUIT));
        $this->add->quote($contact, $fursuitSpec);
    }
}
