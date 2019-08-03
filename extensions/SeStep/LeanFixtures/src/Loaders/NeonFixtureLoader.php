<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

use Nette\FileNotFoundException;
use Nette\Neon\Neon;

class NeonFixtureLoader implements FixtureLoader
{
    /** @var string */
    private $file;

    public function __construct(string $file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException("File '$file' could not be found");
        }

        $this->file = $file;
    }

    /**
     * @return FixtureGroup[]
     */
    public function getGroups()
    {
        $groups = $this->parseFile($this->file);

        return $groups;
    }

    public function getName(): string
    {
        return __CLASS__ . '[' . $this->file . ']';
    }

    private function parseFile(string $file): array
    {
        $data = Neon::decode(file_get_contents($file));

        return array_map([$this, 'parseGroup'], $data);
    }

    private function parseGroup($groupData): StructuredFixtureGroup
    {
        $class = $groupData['class'];
        $data = $groupData['data'];

        return new StructuredFixtureGroup($class, $data);
    }
}
