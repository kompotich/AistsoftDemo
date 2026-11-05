<?php

declare(strict_types=1);

namespace AistsoftDemo\Database;

use AistsoftDemo\Database\Entity\Entity;
use AistsoftDemo\Database\Entity\BankruptcyMessagesEntity;
use AistsoftDemo\Database\Entity\BankEntity;
use AistsoftDemo\Database\Entity\CompanyEntity;
use AistsoftDemo\Database\Entity\ContractsEntity;
use AistsoftDemo\Database\Entity\EnforcementsEntity;
use AistsoftDemo\Database\Entity\EntrepreneurEntity;
use AistsoftDemo\Database\Entity\FedresursEntity;
use AistsoftDemo\Database\Entity\FinancesEntity;
use AistsoftDemo\Database\Entity\InspectionsEntity;
use AistsoftDemo\Database\Entity\LegalCasesEntity;
use AistsoftDemo\Database\Entity\PersonEntity;
use AistsoftDemo\Database\Entity\ReliabilityEntity;
use AistsoftDemo\Database\Entity\SearchEntity;
use AistsoftDemo\Database\Entity\TimelineEntity;
use JsonException;
use PDO;
use PDOException;

class AistsoftDemoRepository
{
    private const ENTITY_CLASSES = [
        'legal-cases' => LegalCasesEntity::class,
        'bank' => BankEntity::class,
        'contracts' => ContractsEntity::class,
        'entrepreneur' => EntrepreneurEntity::class,
        'company' => CompanyEntity::class,
        'timeline' => TimelineEntity::class,
        'reliability' => ReliabilityEntity::class,
        'person' => PersonEntity::class,
        'search' => SearchEntity::class,
        'inspections' => InspectionsEntity::class,
        'bankruptcy-messages' => BankruptcyMessagesEntity::class,
        'fedresurs' => FedresursEntity::class,
        'finances' => FinancesEntity::class,
        'enforcements' => EnforcementsEntity::class,
    ];

    public function __construct(private SqliteDatabase $database)
    {
    }

    public static function entityClasses(): array
    {
        return array_values(self::ENTITY_CLASSES);
    }

    public function save(Entity $entity): Entity
    {
        $entityClass = $entity::class;

        try {
            $requestParametersJson = json_encode($entity->requestParameters, JSON_THROW_ON_ERROR);
            $tableName = $entityClass::tableName();
            $columns = array_merge(
                ['request_parameters_json', 'fetched_at'],
                array_keys($entity->values)
            );
            $quotedColumns = array_map(static fn (string $column): string => '"' . $column . '"', $columns);
            $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);
            $values = [
                'request_parameters_json' => $requestParametersJson,
                'fetched_at' => $entity->fetchedAt,
            ] + $entity->values;
            $statement = $this->database->connection()->prepare(
                sprintf(
                    'INSERT INTO "%s" (%s) VALUES (%s)',
                    $tableName,
                    implode(', ', $quotedColumns),
                    implode(', ', $placeholders)
                )
            );
            $statement->execute($values);
            $entity->id = (int) $this->database->connection()->lastInsertId();
        } catch (JsonException|PDOException $exception) {
            throw new DatabaseException('Не удалось сохранить ответ внешнего API', 0, $exception);
        }

        return $entity;
    }

    public function find(string $entityClass, int $id): ?Entity
    {
        if (!is_a($entityClass, Entity::class, true)) {
            throw new DatabaseException('Класс сущности должен наследовать Entity');
        }

        if ($id < 1) {
            return null;
        }

        try {
            $statement = $this->database->connection()->prepare(
                sprintf('SELECT * FROM "%s" WHERE id = :id', $entityClass::tableName())
            );
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetch();
            if ($row === false) {
                return null;
            }

            $entity = new $entityClass();
            $entity->id = (int) $row['id'];
            $entity->requestParameters = json_decode(
                $row['request_parameters_json'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            $entity->fetchedAt = $row['fetched_at'];
            unset($row['id'], $row['request_parameters_json'], $row['fetched_at']);
            $entity->values = $row;

            return $entity;
        } catch (JsonException|PDOException $exception) {
            throw new DatabaseException('Не удалось прочитать сохраненный ответ из БД', 0, $exception);
        }
    }
}
