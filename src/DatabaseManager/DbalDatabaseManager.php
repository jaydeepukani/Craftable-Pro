<?php

namespace Brackets\CraftablePro\DatabaseManager;

use Illuminate\Support\Facades\Schema;

class DbalDatabaseManager implements DatabaseManager
{
    public function listTableNames(): array
    {
        return Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
    }

    public function getNotNull($tableName, $column): bool
    {
        return Schema::getConnection()->getDoctrineColumn($tableName, $column)->getNotnull();
    }

    public function getColumnType($tableName, $column): string
    {
        return Schema::getColumnType($tableName, $column);
    }
}
