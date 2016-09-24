<?php

namespace App\Utils\Migrations;


use App\Utils\EntityInitializer;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Options\IOptions;

class ProjectInitialiseMigration extends BaseMigration
{
    public function __construct(EntityInitializer $initializer)
    {
        parent::__construct($initializer);
        $this->title = 'Project initialisation';
        $this->description = 'Creates basic entities for proper functionality';
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
        $this->log->writeln($this->add->option(IOptions::TYPE_BOOL, 'Enable quotes', true, null, $sections['paf.quotes']));
    }

    private function addUsers()
    {
        $this->log->writeln($this->add->user('Toanir', 'test'));
    }
}
