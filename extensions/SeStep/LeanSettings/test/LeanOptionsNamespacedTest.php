<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use SeStep\LeanCommon\LeanQueryFilter;
use PAF\Utils\LeanAwareTest;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\LeanSettings\LeanOptionsAdapter;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class LeanOptionsNamespacedTest extends GenericOptionsTest
{
    use LeanAwareTest;

    protected function setUp(): void
    {
        $this->truncateEntityTable(OptionNode::class);
        $this->markTestSkipped("Namespaced options currently not supported");
    }

    protected function getOptions(): IOptionsAdapter
    {
        $entityFactory = new DefaultEntityFactory();

        $repo = new OptionNodeRepository(
            self::$leanConnection,
            self::$leanMapper,
            $entityFactory,
            new LeanQueryFilter(self::$leanMapper),
        );

        return new LeanOptionsAdapter($repo, 'test');
    }
}
