<?php

namespace Brackets\CraftablePro\DatabaseManager;

class MySQLTypesMapper
{
    public static function getTypesMapping(): array
    {
        return [
            'bigint' => Types::BIGINT,
            'binary' => Types::BINARY,
            'blob' => Types::BLOB,
            'char' => Types::STRING,
            'date' => Types::DATE_MUTABLE,
            'datetime' => Types::DATETIME_MUTABLE,
            'decimal' => Types::DECIMAL,
            'double' => Types::FLOAT,
            'float' => Types::FLOAT,
            'int' => Types::INTEGER,
            'integer' => Types::INTEGER,
            'json' => Types::JSON,
            'longblob' => Types::BLOB,
            'longtext' => Types::TEXT,
            'mediumblob' => Types::BLOB,
            'mediumint' => Types::INTEGER,
            'mediumtext' => Types::TEXT,
            'numeric' => Types::DECIMAL,
            'real' => Types::FLOAT,
            'set' => Types::SIMPLE_ARRAY,
            'smallint' => Types::SMALLINT,
            'string' => Types::STRING,
            'text' => Types::TEXT,
            'time' => Types::TIME_MUTABLE,
            'timestamp' => Types::DATETIME_MUTABLE,
            'tinyblob' => Types::BLOB,
            'tinyint' => Types::BOOLEAN,
            'tinytext' => Types::TEXT,
            'varbinary' => Types::BINARY,
            'varchar' => Types::STRING,
            'year' => Types::DATE_MUTABLE,
        ];
    }
}
