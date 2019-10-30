<?php declare(strict_types=1);

namespace Test\PAF\Common\Model;

use PAF\Common\Model\LeanSnapshots;
use PAF\Modules\CommonModule\Model\Person;
use PHPUnit\Framework\TestCase;

class LeanSnapshotsTest extends TestCase
{
    public function testStoreRestore()
    {
        $snapshots = new LeanSnapshots();

        $entity = new Person();
        $entity->id = '42';
        $entity->displayName = 'Jorge';

        $snapshots->store($entity);

        $restored = $snapshots->retrieve($entity);

        $this->assertEquals([
            'id' => 42,
            'displayName' => 'Jorge',
        ], $restored);
    }

    public function testRestoreNonExisting()
    {
        $snapshots = new LeanSnapshots();

        $this->assertNull($snapshots->retrieve(new Person()));
    }

    public function testCompare()
    {
        $snapshots = new LeanSnapshots();

        $entity = new Person();
        $entity->id = '42';
        $entity->displayName = 'Jorge';

        $snapshots->store($entity);

        $this->assertEquals([], $snapshots->compare($entity));

        $entity->displayName = 'Bruno';

        $this->assertEquals([
            'displayName' => 'Jorge',
        ], $snapshots->compare($entity));
    }
}
