<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use PAF\Utils\TestDBUtils;
use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\LeanSettings\LeanValuePoolsAdapter;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\SeStep\GeneralSettings\GenericValuePoolTest;

class LeanValuePoolsTest extends GenericValuePoolTest
{
    protected function setUp(): void
    {
        TestDBUtils::truncateEntityTable(OptionNode::class);
    }

    /** @return IValuePoolsAdapter */
    protected function getPoolAdapter()
    {
        $connection = TestDBUtils::getLeanConnection();
        $mapper = TestDBUtils::getLeanMapper();
        $entityFactory = new DefaultEntityFactory();

        $repo = new OptionNodeRepository($connection, $mapper, $entityFactory);

        return new LeanValuePoolsAdapter($repo);
    }
}
