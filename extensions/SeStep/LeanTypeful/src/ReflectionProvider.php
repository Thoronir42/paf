<?php declare(strict_types=1);

namespace SeStep\LeanTypeful;

use Dibi\Connection;

class ReflectionProvider
{
    /** @var Connection */
    private $connection;
    /** @var string */
    private $schemaName;

    public function __construct(Connection $connection, string $schemaName)
    {
        $this->connection = $connection;
        $this->schemaName = $schemaName;
    }

    public function getColumns(string $tableName, array $columnWhitelist = [])
    {
        $columnTypes = [];

        $columnsQuery = $this->connection->select('*')
            ->from('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA LIKE %s', $this->schemaName)
            ->where('TABLE_NAME LIKE %s', $tableName);
        if (!empty($columnWhitelist)) {
            $columnsQuery->where('COLUMN_NAME IN (?)', $columnWhitelist);
        }

        $columns = $columnsQuery->fetchAll();

        if (empty($columns)) {
            $tableExists = $this->connection->select('COUNT(TABLE_NAME)')
                ->from('information_schema.TABLES')
                ->where('TABLE_SCHEMA LIKE %s', $this->schemaName)
                ->where('TABLE_NAME = %s', $tableName)
                ->fetchSingle();

            if (!$tableExists) {
                trigger_error("Table '{$this->schemaName}.$tableName' does not exist");
            }
        }

        foreach ($columns as $column) {
            $columnTypes[$column['COLUMN_NAME']] = $this->inferColumnType($column);
        }

        if (!empty($columnWhitelist) && count($columns) !== $columnWhitelist) {
            $missingColumns = array_diff_key(array_flip($columnWhitelist), $columnTypes);
            $missingColumnsStr = "['" . implode("', '", array_flip($missingColumns)) . "']";
            trigger_error("These columns could not be retrieved table '$tableName': " . $missingColumnsStr);
        }

        return $columnTypes;
    }

    private function inferColumnType($column): array
    {
        $options = [];
        if (!$column['IS_NULLABLE']) {
            $options['required'] = true;
        }
        $dataType = $column['DATA_TYPE'];
        switch ($column['DATA_TYPE']) {
            case 'varchar':
            case 'text':
                $type = 'typeful.text';
                $options['maxLength'] = $column['CHARACTER_MAXIMUM_LENGTH'];
                if ($dataType === 'text') {
                    $options['richText'] = true;
                }
                break;

            case 'int':
                $type = 'typeful.int';
                break;

            default:
                throw new \UnexpectedValueException("Unknown type $dataType");
        }

        return [
            'type' => $type,
            'options' => $options,
        ];
    }
}
