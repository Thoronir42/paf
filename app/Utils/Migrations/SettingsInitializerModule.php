<?php declare(strict_types=1);

namespace PAF\Utils\Migrations;


use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IOptionSection;

class SettingsInitializerModule
{
    /** @var SettingsEntityCreator */
    private $add;

    protected function setup()
    {
        $this->add = new SettingsEntityCreator($this->provider, $this->output);
    }

    public function run()
    {
        $sections = $this->addSections();
        $this->addOptions($sections);
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
     * @param IOptionSection[] $sections
     */
    private function addOptions($sections = [])
    {
        $this->add->option(IOption::TYPE_BOOL, 'Enable quotes', true, null, $sections['paf.quotes']);
        $this->add->option(IOption::TYPE_STRING, 'Preffered species', '', null, $sections['paf.quotes']);
        $this->add->option(IOption::TYPE_INT, 'Base suit price', 420, null, $sections['paf.priceList']);
        $this->add->option(IOption::TYPE_INT, 'Extra feature', 50, null, $sections['paf.priceList']);
    }
}
