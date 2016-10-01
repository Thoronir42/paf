<?php

namespace App\Utils\Migrations;


use SeStep\Migrations\Base\InitializerModuleBase;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Options\IOptions;

class CoreInitializerModule extends InitializerModuleBase
{
    /** @var CoreEntityInitializer */
    private $add;

    protected function setup()
    {
        $this->add = new CoreEntityInitializer($this->provider, $this->output);
    }

    public function run()
    {
        $sections = $this->addSections();
        $this->addOptions($sections);
        $this->addUsers();
    }

    private function addSections()
    {
        $sections = [
            'paf.quotes' => $this->add->section('paf.quotes', 'Quotes related settings'),
        ];

        return $sections;
    }

    /**
     * @param OptionsSection[] $sections
     */
    private function addOptions($sections = [])
    {
        $this->add->option(IOptions::TYPE_BOOL, 'Enable quotes', true, null, $sections['paf.quotes']);
    }

    private function addUsers()
    {
        $this->add->user('Toanir', 'test');
    }
}
