<?php

namespace SeStep\GeneralSettings;

use SeStep\GeneralSettings\Exceptions\ValuePoolAlreadyExistsException;

interface IValuePoolsAdapter
{
    /**
     * Retrieves value pool by its name
     *
     * @param string $name
     * @return Model\IValuePool|null
     */
    public function getPool(string $name);

    /**
     * Creates a value pool
     *
     * @param string $name
     * @param array $values
     *
     * @return Model\IValuePool
     *
     * @throws ValuePoolAlreadyExistsException
     */
    public function createPool(string $name, array $values): Model\IValuePool;

    /**
     * Updates values of given pool
     *
     * @param Model\IValuePool $pool
     * @param array $values
     * @return mixed
     */
    public function updateValues(Model\IValuePool $pool, array $values);

    /**
     * Deletes value pool
     *
     * @param Model\IValuePool|string $pool - pool or name of pool to be removed
     *
     * @return bool
     */
    public function deletePool($pool): bool;


    /**
     * Sets options value pool
     *
     * @param Model\IOption $option
     * @param Model\IValuePool|null $pool
     *
     * @return bool
     */
    public function setOptionsPool(Model\IOption $option, ?Model\IValuePool $pool);
}
