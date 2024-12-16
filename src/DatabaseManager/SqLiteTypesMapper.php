<?php

namespace Brackets\CraftablePro\DatabaseManager;

class SqLiteTypesMapper
{
    public static function getTypesMapping(): array
    {
        return [
            'bigint' => Types::BIGINT,
            'bigserial' => Types::BIGINT,
            'blob' => Types::BLOB,
            'boolean' => Types::BOOLEAN,
            'char' => Types::STRING,
            'clob' => Types::TEXT,
            'date' => Types::DATE_MUTABLE,
            'datetime' => Types::DATETIME_MUTABLE,
            'decimal' => Types::DECIMAL,
            'double' => Types::FLOAT,
            'double precision' => Types::FLOAT,
            'float' => Types::FLOAT,
            'image' => Types::STRING,
            'int' => Types::INTEGER,
            'integer' => Types::INTEGER,
            'longtext' => Types::TEXT,
            'longvarchar' => Types::STRING,
            'mediumint' => Types::INTEGER,
            'mediumtext' => Types::TEXT,
            'ntext' => Types::STRING,
            'numeric' => Types::DECIMAL,
            'nvarchar' => Types::STRING,
            'real' => Types::FLOAT,
            'serial' => Types::INTEGER,
            'smallint' => Types::SMALLINT,
            'text' => Types::TEXT,
            'time' => Types::TIME_MUTABLE,
            'timestamp' => Types::DATETIME_MUTABLE,
            'tinyint' => Types::BOOLEAN,
            'tinytext' => Types::TEXT,
            'varchar' => Types::STRING,
            'varchar2' => Types::STRING,
        ];
    }
}
