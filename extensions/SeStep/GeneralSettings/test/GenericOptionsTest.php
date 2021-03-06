<?php declare(strict_types=1);
namespace Test\SeStep\GeneralSettings;

use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\Model\IOption;
use SeStep\GeneralSettings\Model\IOptionSection;
use SeStep\GeneralSettings\Model\IOptionSectionWritable;
use SeStep\LeanSettings\Model\Section;

abstract class GenericOptionsTest extends TestCase
{
    abstract protected function getOptions(): IOptionsAdapter;

    public function testSetOptions()
    {
        $options = $this->getOptions();

        $options->setValue('FFA', 'gameMode');
        $options->setValue(60, 'timeLimit');
        $options->setValue(true, 'respawns');

        $this->assertCount(3, $options);

        $options->setValue('1v1', 'gameMode');
        $this->assertCount(3, $options);

        $nodes = $options->getNodes();
        $this->assertEquals(IOption::TYPE_STRING, $nodes['gameMode']->getType());
        $this->assertEquals('1v1', $nodes['gameMode']->getValue());

        $this->assertEquals(IOption::TYPE_INT, $nodes['timeLimit']->getType());

        $this->assertEquals(IOption::TYPE_BOOL, $nodes['respawns']->getType());
    }

    private function setEntrances(IOptionsAdapter $options)
    {
        $entrancesSection = $options->addSection('entrances');

        $this->assertInstanceOf(IOptionSection::class, $entrancesSection);
        $this->assertCount(0, $entrancesSection->getNodes());

        $options->setValue('broken window', 'entrances.main');
        $options->setValue('hole in a wall', 'entrances.side');

        $this->assertCount(1, $options->getNodes());
    }

    public function testSetNode()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        $entrancesSection = $options->getNodes()['entrances'];
        $this->assertInstanceOf(IOptionSection::class, $entrancesSection);
        $this->assertCount(2, $entrancesSection->getNodes());
    }

    public function testGetValueThroughSection()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        /** @var IOptionSection $entrancesSection */
        $entrancesSection = $options->getNode('entrances');
        $this->assertInstanceOf(IOptionSection::class, $entrancesSection);
        $this->assertCount(2, $entrancesSection);

        $this->assertEquals('broken window', $entrancesSection->getNodes()['main']->getValue());
    }

    public function testGetValueDirectlyFromOptions()
    {
        $options = $this->getOptions();
        $this->setEntrances($options);

        $this->assertEquals('hole in a wall', $options->getValue('entrances.side'));
    }

    public function testAddValueRoot()
    {
        $options = $this->getOptions();

        $this->assertCount(0, $options);

        $options->addValue('can');
        $this->assertCount(1, $options);

        $options->addValue('can can');
        $this->assertCount(2, $options);

        $options->addValue('can can dance');
        $this->assertCount(3, $options);
    }

    public function testUnset()
    {
        $options = $this->getOptions();

        $options->addValue('Huey');
        $options->addValue('Dewey');
        $options->addValue('Louie');

        $options->removeNode("1");

        $this->assertCount(2, $options);

        $this->assertEquals([0, 2], array_keys($options->getNodes()));
    }

    public function testAddValueInSection()
    {
        $options = $this->getOptions();

        $options->addValue('tweety', 'nest');
        $options->addValue('pecky', 'nest');
        $options->addValue('bob', 'nest');

        $this->assertCount(1, $options);
        $this->assertCount(3, $options->getNode('nest'));
    }

    public function testUnsetInSection()
    {
        $options = $this->getOptions();

        $options->addValue('Scar', 'lions');
        $options->addValue('Mufasa', 'lions');
        $options->addValue('Simba', 'lions');

        $options->removeNode('lions.1'); // Long live the king

        $this->assertCount(1, $options);
        $remainingLions = [];
        foreach ($options->getNode('lions') as $lionNode) {
            assert($lionNode instanceof IOption);
            $remainingLions[] = $lionNode->getValue();
        }
        $this->assertEquals(['Scar', 'Simba'], $remainingLions);
    }

    public function testSetNestedValue()
    {
        $options = $this->getOptions();

        $options->setValue(42, 'answers.lifeUniverseAndEverything.ultimateAnswer.intValue');

        $answersSection = $this->getSectionNode($options, 'answers');
        $luaeSection = $this->getSectionNode($answersSection, 'lifeUniverseAndEverything');
        $ultimateAnswerSection = $this->getSectionNode($luaeSection, 'ultimateAnswer');
        $valueNode = $this->getOptionNode($ultimateAnswerSection, 'intValue');

        $this->assertEquals(42, $valueNode->getValue());
    }

    public function testGetNestedValue()
    {
        $options = $this->getOptions();

        /** @var IOptionSection|IOptionSectionWritable $tableSection */
        $tableSection = $options->addSection('room.table');
        $this->skipIfSectionUnwritable($tableSection);

        $tableSection->setValue('full', 'drawer');

        $this->assertEquals('full', $options->getValue('room.table.drawer'));
    }

    public function testPropagationThroughSections()
    {
        $options = $this->getOptions();

        /** @var IOptionSection|IOptionSectionWritable $section */
        $section = $options->addSection('entertainment');
        $this->skipIfSectionUnwritable($section);

        $section->setValue('SPIELSTATION', 'console');

        $this->assertEquals('SPIELSTATION', $section->getValue('console'));

        $options->setValue('X-CRATE', 'entertainment.console');
        $this->assertEquals('X-CRATE', $options->getValue('entertainment.console'));
        $this->assertEquals('X-CRATE', $section->getValue('console'));
    }

    /**
     * @param IOptionSection|IOptionSectionWritable $section
     */
    protected function skipIfSectionUnwritable(IOptionSection $section)
    {
        if (!$section instanceof IOptionSectionWritable) {
            $this->markTestSkipped("Section not writable");
        }
    }

    protected function getSectionNode(IOptionSection $parent, $name): IOptionSection
    {
        $section = $parent->getNode($name);
        $this->assertInstanceOf(IOptionSection::class, $section);

        return $section;
    }

    protected function getOptionNode(IOptionSection $parent, $name): IOption
    {
        $section = $parent->getNode($name);
        $this->assertInstanceOf(IOption::class, $section);

        return $section;
    }
}
