<?php declare(strict_types=1);
namespace SeStep\GeneralSettingsJson;

use SeStep\GeneralSettingsInMemory\InMemoryOptionsAdapter;

class Serializer
{
    final public function save(InMemoryOptionsAdapter $options, string $filename)
    {
        $serialized = $this->serialize($options->getData());

        file_put_contents($filename, $serialized);
    }

    protected function serialize(array &$data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    final public function load(InMemoryOptionsAdapter $options, string $filename)
    {
        $serialized = file_get_contents($filename);

        $options->setData($this->deserialize($serialized));
    }

    protected function &deserialize($str)
    {
        $data = json_decode($str);

        return $data;
    }
}
