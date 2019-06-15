<?php

namespace SeStep\GeneralSettings;


use RuntimeException;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\SettingsInterface\Exceptions\NotFoundException;

class LazySectionVisitor implements \ArrayAccess
{
    /** @var IOptionSection */
    public $source;

    private $options;
    private $sections;

    public function __construct(IOptionSection $source)
    {
        $this->source = $source;
    }

    /**
     * @param bool $clean forces optios reload
     * @return Options\ReadOnlyOption[]
     */
    public function getOptions($clean = false)
    {
        if ($clean || !$this->options) {
            $this->options = [];

            foreach ($this->source->getNodes() as $name => $node) {
                if ($node->getType() === IOptionSection::TYPE_SECTION) {
                    continue;
                }

                $this->options[$name] = $node;
            }
        }

        return $this->options;
    }

    /**
     * @param bool $clean forces sections reload
     * @return Options\IOptionSection[]
     */
    public function getSections($clean = false)
    {
        if ($clean || !$this->sections) {
            $this->sections = [];
            foreach ($this->source->getNodes() as $name => $node) {
                if ($node->getType() === IOptionSection::TYPE_SECTION) {
                    $this->sections[$name] = $node;
                }
            }
        }

        return $this->sections;
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->source->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}
