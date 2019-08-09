<?php declare(strict_types=1);

namespace Test\SeStep\LeanSettings;

use LeanMapper\DefaultEntityFactory;
use SeStep\GeneralSettings\IOptions;
use SeStep\LeanSettings\LeanOptions;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Repository\OptionNodeRepository;
use Test\PAF\Utils\TestDBUtils;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class LeanOptionsTest extends GenericOptionsTest
{
    protected function setUp(): void
    {
        TestDBUtils::truncateEntityTable(OptionNode::class);
    }

    protected function getOptions(): IOptions
    {
        $connection = TestDBUtils::getLeanConnection();
        $mapper = TestDBUtils::getLeanMapper();
        $entityFactory = new DefaultEntityFactory();

        $repo = new OptionNodeRepository($connection, $mapper, $entityFactory);

        return new LeanOptions($repo);
    }
}
