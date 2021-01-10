<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use SeStep\LeanCommon\LeanQueryFilter;
use PAF\Utils\LeanAwareTest;
use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\LeanSettings\LeanValuePoolsAdapter;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\SeStep\GeneralSettings\GenericValuePoolTest;

class LeanValuePoolsTest extends GenericValuePoolTest
{
    use LeanAwareTest;

    protected function setUp(): void
    {
        $this->truncateEntityTable(OptionNode::class);
    }

    /** @return IValuePoolsAdapter */
    protected function getPoolAdapter()
    {
        $entityFactory = new DefaultEntityFactory();
        $leanQueryFilter = new LeanQueryFilter(self::$leanMapper);
        $repo = new OptionNodeRepository(self::$leanConnection, self::$leanMapper, $entityFactory, $leanQueryFilter);

        return new LeanValuePoolsAdapter($repo);
    }
}
