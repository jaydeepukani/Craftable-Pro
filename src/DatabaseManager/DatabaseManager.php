<?php

namespace Brackets\CraftablePro\DatabaseManager;

interface DatabaseManager
{
    public function listTableNames(): array;

    public function getNotNull($tableName, $column): bool;

    public function getColumnType($tableName, $column): ?string;
}
