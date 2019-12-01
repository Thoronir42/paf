<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use PAF\Utils\TestDBUtils;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\LeanSettings\LeanOptionsAdapter;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class LeanOptionsNamespacedTest extends GenericOptionsTest
{
    protected function setUp(): void
    {
        TestDBUtils::truncateEntityTable(OptionNode::class);
        $this->markTestSkipped("Namespaced options currently not supported");
    }

    protected function getOptions(): IOptionsAdapter
    {
        $connection = TestDBUtils::getLeanConnection();
        $mapper = TestDBUtils::getLeanMapper();
        $entityFactory = new DefaultEntityFactory();

        $repo = new OptionNodeRepository($connection, $mapper, $entityFactory);

        return new LeanOptionsAdapter($repo, 'test');
    }
}
