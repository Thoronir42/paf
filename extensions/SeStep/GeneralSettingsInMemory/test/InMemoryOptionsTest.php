<?php


namespace Test\SeStep\GeneralSettingsInMemory;


use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettingsInMemory\InMemoryOptions;

class InMemoryOptionsTest extends TestCase
{
    public function testSetOptions()
    {
        $options = new InMemoryOptions();

        $options['gameMode'] = '1v1';
        $options['timeLimit'] = 60;
        $options['respawns'] = true;

        $nodes = $options->getNodes();
        $this->assertCount(3, $nodes);

        $this->assertEquals(IOption::TYPE_STRING, $nodes['gameMode']->getType());
        $this->assertEquals('1v1', $nodes['gameMode']->getValue());

        $this->assertEquals(IOption::TYPE_INT, $nodes['timeLimit']->getType());

        $this->assertEquals(IOption::TYPE_BOOL, $nodes['respawns']->getType());
    }

    public function testSetNode()
    {
        $options = new InMemoryOptions();

        $entrancesSection = $options->addSection('entrances');

        $entrancesSection['main'] = 'broken window';
        $entrancesSection['side'] = 'hole in a wall';

        $this->assertCount(1, $options->getNodes());
        $this->assertCount(2, $options['entrances']->getNodes());

        $this->assertEquals('broken window', $options['entrances']['main']->getValue());
        $this->assertEquals('hole in a wall', $options['entrances']['side']->getValue());
    }

    public function testUnset()
    {
        $options = new InMemoryOptions();

        $options[] = 'can';
        $options[] = 'can';
        $options[] = 'the dance';

        $this->assertCount(3, $options);

        unset($options[1]);

        $this->assertCount(2, $options);

        $this->assertEquals([0, 2], array_keys($options->getNodes()));
    }

    public function testSetNestedValue()
    {
        $options = new InMemoryOptions();

        $options->setValue(42, 'answers.lifeUniverseAndEverything.result');

        $this->assertEquals(42, $options['answers']['lifeUniverseAndEverything']['result']->getValue());
    }

    public function testGetNestedValue()
    {
        $options = new InMemoryOptions();

        $options->addSection('room')->addSection('table')['drawer'] = 'full';

        $this->assertEquals('full', $options->getValue('room.table.drawer'));
    }
}