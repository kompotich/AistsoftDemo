<?php

declare(strict_types=1);

namespace AistsoftDemo\Database;

use AistsoftDemo\Database\Entity\Entity;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

class SqliteDatabase
{
    private PDO $connection;

    public function __construct(string $path, array $entities)
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new RuntimeException('Для работы приложения требуется расширение PDO SQLite');
        }

        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Не удалось создать каталог базы данных SQLite');
        }

        try {
            $this->connection = new PDO('sqlite:' . $path);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->exec('PRAGMA foreign_keys = ON');
            $this->connection->exec('PRAGMA busy_timeout = 5000');
            $this->initializeSchema($entities);
        } catch (PDOException $exception) {
            throw new DatabaseException('Не удалось инициализировать базу данных SQLite', 0, $exception);
        }
    }

    public function connection(): PDO
    {
        return $this->connection;
    }

    private function initializeSchema(array $entities): void
    {
        $schemaStatements = self::schemaStatements($entities);
        $this->connection->beginTransaction();
        try {
            foreach ($schemaStatements as $statement) {
                $this->connection->exec($statement);
            }

            $this->connection->commit();
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            if ($exception instanceof DatabaseException) {
                throw $exception;
            }

            throw new DatabaseException('Не удалось создать схему SQLite', 0, $exception);
        }
    }

    public static function schemaStatements(array $entities): array
    {
        $statements = [];

        foreach ($entities as $entityClass) {
            if (!is_a($entityClass, Entity::class, true)) {
                throw new DatabaseException('Класс сущности должен наследовать Entity');
            }

            $tableName = $entityClass::tableName();
            if (preg_match('/^[a-z][a-z0-9_]*$/', $tableName) !== 1) {
                throw new DatabaseException('Недопустимое имя таблицы сущности');
            }

            $columns = $entityClass::columns();
            $definitions = [
                'id INTEGER PRIMARY KEY AUTOINCREMENT',
                'request_parameters_json TEXT NOT NULL',
                'fetched_at TEXT NOT NULL',
            ];
            $reservedColumns = array_fill_keys(['id', 'request_parameters_json', 'fetched_at'], true);
            foreach ($columns as $columnName => $definition) {
                if (
                    preg_match('/^[a-z][a-z0-9_]*$/', $columnName) !== 1
                    || !in_array($definition['type'] ?? null, ['TEXT', 'INTEGER', 'REAL'], true)
                    || !is_string($definition['path'] ?? null)
                    || isset($reservedColumns[$columnName])
                ) {
                    throw new DatabaseException('Недопустимое описание столбца сущности');
                }

                $definitions[] = sprintf('"%s" %s', $columnName, $definition['type']);
            }

            $statements[] = sprintf(
                'CREATE TABLE IF NOT EXISTS "%s" (%s)',
                $tableName,
                implode(', ', $definitions)
            );
            $statements[] = sprintf(
                'CREATE INDEX IF NOT EXISTS "idx_%s_fetched_at" ON "%s" (fetched_at)',
                $tableName,
                $tableName
            );
        }

        return $statements;
    }
}
