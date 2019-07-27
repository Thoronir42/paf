<?php declare(strict_types=1);

namespace PAF\Utils\Migrations;

use PAF\Common\Model\Embeddable\Contact;
use PAF\Common\Model\Embeddable\FursuitSpecification;
use PAF\Common\Model\Entity\Fursuit;
use SeStep\Migrations\Base\InitializerModuleBase;

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
        $this->addUsers();
        $this->addQoutes();
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
