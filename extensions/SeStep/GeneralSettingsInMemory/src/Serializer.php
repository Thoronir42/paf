<?php

namespace SeStep\GeneralSettingsJson;


use SeStep\GeneralSettingsInMemory\InMemoryOptions;

class Serializer
{
    public final function save(InMemoryOptions $options, string $filename)
    {
        $serialized = $this->serialize($options->getData());

        file_put_contents($filename, $serialized);
    }

    protected function serialize(array &$data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public final function load(InMemoryOptions $options, string $filename) {
        $serialized = file_get_contents($filename);

        $options->setData($this->deserialize($serialized));
    }

    protected function &deserialize($str)
    {
        $data = json_decode($str);

        return $data;
    }
}