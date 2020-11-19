<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use League\Flysystem\Filesystem;
use PAF\Utils\TestingStorage;
use PAF\Utils\FileResourceTest;
use PHPUnit\Framework\TestCase;
use SeStep\NetteTypeful\Types\FileDelete;
use SeStep\NetteTypeful\Types\FileType;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;
use SeStep\Typeful\Service\EntityDescriptorRegistry;
use SeStep\Typeful\TestDoubles\RegistryFactory;
use Tracy\Debugger;

class TypefulRepositoryTest extends TestCase
{
    use TestingStorage;
    use FileResourceTest;

    /** @var Filesystem */
    private $previewImageStorage;
    private $entityRegistry;
    private $typeRegistry;

    protected function setUp(): void
    {
        Debugger::$showLocation = true;
        if (!$this->previewImageStorage) {
            $this->previewImageStorage = $this->getStorage(self::class);
        }
        $this->clearStorage($this->previewImageStorage);

        if (!$this->typeRegistry) {
            $this->typeRegistry = RegistryFactory::createTypeRegistry([
                'file' => new FileType(),
            ]);
        }

        if (!$this->entityRegistry) {
            $this->entityRegistry =  new EntityDescriptorRegistry([
                'common.testDummy' => new GenericDescriptor([
                    'os' => new Property('text', [
                        'default' => 'Win',
                    ]),
                    'version' => new Property('int'),
                    'previewImage' => new Property('file', [
                        'storage' => $this->previewImageStorage,
                        'preferredName' => '!os',
                    ]),
                ]),
            ]);
        }
    }


    private function createTestInstance(): DummyTypefulRepository
    {
        $dummy = new DummyTypefulRepository();
        $dummy->injectTypefulRegistry($this->entityRegistry, $this->typeRegistry);

        return $dummy;
    }

    public function testCreate()
    {
        $dummy = $this->createTestInstance();
        $dummy->createNewFromTypefulData([
            'version' => 42,
            'previewImage' => $this->createFileUpload('dog_tmp.png', 'dog.png'),
        ]);

        $persisted = $dummy->getPersistedEntities();

        self::assertCount(1, $persisted);
        self::assertEquals([
            'os' => 'Win',
            'version' => 42,
            'previewImage' => 'Win.png'
        ], $persisted[0]->getRowData());

        self::assertTrue($this->previewImageStorage->has('Win.png'), "File 'Win.png' should exist");
        self::assertCount(1, $this->previewImageStorage->listContents());
    }

    public function testUpdateDeleteFile()
    {
        $file = $this->createFileUpload('dog_tmp.png');
        $this->previewImageStorage->write('ubuntu.png', $file->getContents());

        if (count($this->previewImageStorage->listContents()) !== 1 || !$this->previewImageStorage->has('ubuntu.png')) {
            $this->fail("Could not initialize context");
        }
        $entity = new DummyEntity();
        $entity->previewImage = 'ubuntu.png';
        $entity->os = 'ubuntu';
        $entity->version = 20;

        $dummy = $this->createTestInstance();
        $dummy->updateWIthTypefulData($entity, [
            'previewImage' => new FileDelete('ubuntu.png'),
        ]);

        $persisted = $dummy->getPersistedEntities();

        self::assertCount(1, $persisted);
        self::assertEquals([
            'previewImage' => null,
            'os' => 'ubuntu',
            'version' => 20,
        ], $persisted[0]->getRowData());
        self::assertFalse($this->previewImageStorage->has('ubuntu.png'), "Storage should not contain deleted file");
    }
}
