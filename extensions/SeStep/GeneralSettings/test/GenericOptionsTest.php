<?php


namespace Test\SeStep\GeneralSettings;


use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IOptionSection;

abstract class GenericOptionsTest extends TestCase
{
    protected abstract function getOptions(): IOptions;

    public function testSetOptions()
    {
        $options = $this->getOptions();

        $options->setValue('1v1', 'gameMode');
        $options->setValue(60, 'timeLimit');
        $options->setValue(true, 'respawns');

        $nodes = $options->getNodes();
        $this->assertCount(3, $nodes);

        $this->assertEquals(IOption::TYPE_STRING, $nodes['gameMode']->getType());
        $this->assertEquals('1v1', $nodes['gameMode']->getValue());

        $this->assertEquals(IOption::TYPE_INT, $nodes['timeLimit']->getType());

        $this->assertEquals(IOption::TYPE_BOOL, $nodes['respawns']->getType());
    }

    private function setEntrances(IOptions $options) {
        $entrancesSection = $options->addSection('entrances');

        $this->assertInstanceOf(IOptionSection::class, $entrancesSection);
        $this->assertCount(0, $entrancesSection->getNodes());

        $options->setValue('broken window', 'entrances.main');
        $options->setValue('hole in a wall', 'entrances.side');
    }

    public function testSetNode()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        $this->assertCount(1, $options->getNodes());

        $entrancesSection = $options->getNodes()['entrances'];
        $this->assertInstanceOf(IOptionSection::class, $entrancesSection);
        $this->assertCount(2, $entrancesSection->getNodes());
    }

    public function testGetValueThroughSection()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        $entrancesSection = $options->getNodes()['entrances'];

        $this->assertEquals('broken window', $entrancesSection->getNodes()['main']->getValue());
    }

    public function testGetValueDirectlyFromOptions()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        $this->assertEquals('hole in a wall', $options->getValue('entrances.side'));

    }

    public function testUnset()
    {
        $options = $this->getOptions();

        $options->addValue('can');
        $options->addValue('can');
        $options->addValue('the dance');

        $this->assertCount(3, $options);

        unset($options[1]);

        $this->assertCount(2, $options);

        $this->assertEquals([0, 2], array_keys($options->getNodes()));
    }

    public function testSetNestedValue()
    {
        $options = $this->getOptions();

        $options->setValue(42, 'answers.lifeUniverseAndEverything.result');

        $this->assertEquals(42, $options['answers']['lifeUniverseAndEverything']['result']->getValue());
    }

    public function testGetNestedValue()
    {
        $options = $this->getOptions();

        $options->addSection('room.table')['drawer'] = 'full';

        $this->assertEquals('full', $options->getValue('room.table.drawer'));
    }
}