<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use PAF\Common\Lean\LeanQueryFilter;
use PAF\Utils\LeanAwareTest;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\LeanSettings\LeanOptionsAdapter;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class LeanOptionsTest extends GenericOptionsTest
{
    use LeanAwareTest;

    protected function setUp(): void
    {
        $this->truncateEntityTable(OptionNode::class);
    }

    protected function getOptions(): IOptionsAdapter
    {
        $entityFactory = new DefaultEntityFactory();
        $leanQueryFilter = new LeanQueryFilter(self::$leanMapper);
        $repo = new OptionNodeRepository(self::$leanConnection, self::$leanMapper, $entityFactory, $leanQueryFilter);

        return new LeanOptionsAdapter($repo);
    }
}
