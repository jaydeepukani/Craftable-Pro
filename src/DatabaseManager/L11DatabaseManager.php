<?php

namespace Brackets\CraftablePro\DatabaseManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class L11DatabaseManager implements DatabaseManager
{
    public function listTableNames(): array
    {
        return Schema::getTableListing();
    }

    public function getNotNull($tableName, $column): bool
    {
        return ! collect(Schema::getColumns($tableName))->firstWhere('name', $column)['nullable'];
    }

    /**
     * @throws \Exception
     */
    public function getColumnType($tableName, $column): ?string
    {
        return match (DB::getDriverName()) {
            'pgsql' => PsqlTypesMapper::getTypesMapping()[Schema::getColumnType($tableName, $column)] ?? Types::STRING,
            'mysql' => MySQLTypesMapper::getTypesMapping()[Schema::getColumnType($tableName, $column)] ?? Types::STRING,
            'sqlite' => SqLiteTypesMapper::getTypesMapping()[Schema::getColumnType($tableName, $column)] ?? Types::STRING,
            default => throw new \Exception('Database driver not supported'),
        };
    }
}
