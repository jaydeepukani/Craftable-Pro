<?php

namespace Brackets\CraftablePro\DatabaseManager;

class PsqlTypesMapper
{
    public static function getTypesMapping(): array
    {
        return [
             'bigint' => Types::BIGINT,
             'bigserial' => Types::BIGINT,
             'bool' => Types::BOOLEAN,
             'boolean' => Types::BOOLEAN,
             'bpchar' => Types::STRING,
             'bytea' => Types::BLOB,
             'char' => Types::STRING,
             'date' => Types::DATE_MUTABLE,
             'datetime' => Types::DATETIME_MUTABLE,
             'decimal' => Types::DECIMAL,
             'double' => Types::FLOAT,
             'double precision' => Types::FLOAT,
             'float' => Types::FLOAT,
             'float4' => Types::FLOAT,
             'float8' => Types::FLOAT,
             'inet' => Types::STRING,
             'int' => Types::INTEGER,
             'int2' => Types::SMALLINT,
             'int4' => Types::INTEGER,
             'int8' => Types::BIGINT,
             'integer' => Types::INTEGER,
             'interval' => Types::STRING,
             'json' => Types::JSON,
             'jsonb' => Types::JSON,
             'money' => Types::DECIMAL,
             'numeric' => Types::DECIMAL,
             'serial' => Types::INTEGER,
             'serial4' => Types::INTEGER,
             'serial8' => Types::BIGINT,
             'real' => Types::FLOAT,
             'smallint' => Types::SMALLINT,
             'text' => Types::TEXT,
             'time' => Types::TIME_MUTABLE,
             'timestamp' => Types::DATETIME_MUTABLE,
             'timestamptz' => Types::DATETIMETZ_MUTABLE,
             'timetz' => Types::TIME_MUTABLE,
             'tsvector' => Types::TEXT,
             'uuid' => Types::GUID,
             'varchar' => Types::STRING,
             'year' => Types::DATE_MUTABLE,
             '_varchar' => Types::STRING,
         ];
    }
}
