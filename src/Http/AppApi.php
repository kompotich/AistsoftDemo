<?php

declare(strict_types=1);

namespace AistsoftDemo\Http;

use AistsoftDemo\Database\AistsoftDemoRepository;
use AistsoftDemo\Database\DatabaseException;
use AistsoftDemo\Dto\BankruptcyMessagesDto;
use AistsoftDemo\Dto\BankDto;
use AistsoftDemo\Dto\CompanyDto;
use AistsoftDemo\Dto\ContractsDto;
use AistsoftDemo\Dto\EnforcementsDto;
use AistsoftDemo\Dto\EntrepreneurDto;
use AistsoftDemo\Dto\FedresursDto;
use AistsoftDemo\Dto\FinancesDto;
use AistsoftDemo\Dto\InspectionsDto;
use AistsoftDemo\Dto\LegalCasesDto;
use AistsoftDemo\Dto\PersonDto;
use AistsoftDemo\Dto\ReliabilityDto;
use AistsoftDemo\Dto\SearchDto;
use AistsoftDemo\Dto\TimelineDto;
use AistsoftDemo\Database\Entity\BankruptcyMessagesEntity;
use AistsoftDemo\Database\Entity\BankEntity;
use AistsoftDemo\Database\Entity\CompanyEntity;
use AistsoftDemo\Database\Entity\ContractsEntity;
use AistsoftDemo\Database\Entity\EnforcementsEntity;
use AistsoftDemo\Database\Entity\EntrepreneurEntity;
use AistsoftDemo\Database\Entity\Entity;
use AistsoftDemo\Database\Entity\FedresursEntity;
use AistsoftDemo\Database\Entity\FinancesEntity;
use AistsoftDemo\Database\Entity\InspectionsEntity;
use AistsoftDemo\Database\Entity\LegalCasesEntity;
use AistsoftDemo\Database\Entity\PersonEntity;
use AistsoftDemo\Database\Entity\ReliabilityEntity;
use AistsoftDemo\Database\Entity\SearchEntity;
use AistsoftDemo\Database\Entity\TimelineEntity;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use JsonException;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

class AppApi
{
    private const RESOURCE_METHODS = [
        'search' => [SearchDto::class, 'saveSearch', 'getSearch'],
        'company' => [CompanyDto::class, 'saveCompany', 'getCompany'],
        'entrepreneur' => [EntrepreneurDto::class, 'saveEntrepreneur', 'getEntrepreneur'],
        'person' => [PersonDto::class, 'savePerson', 'getPerson'],
        'finances' => [FinancesDto::class, 'saveFinances', 'getFinances'],
        'reliability' => [ReliabilityDto::class, 'saveReliability', 'getReliability'],
        'timeline' => [TimelineDto::class, 'saveTimeline', 'getTimeline'],
        'legal-cases' => [LegalCasesDto::class, 'saveLegalCases', 'getLegalCases'],
        'enforcements' => [EnforcementsDto::class, 'saveEnforcements', 'getEnforcements'],
        'contracts' => [ContractsDto::class, 'saveContracts', 'getContracts'],
        'fedresurs' => [FedresursDto::class, 'saveFedresurs', 'getFedresurs'],
        'bankruptcy-messages' => [BankruptcyMessagesDto::class, 'saveBankruptcyMessages', 'getBankruptcyMessages'],
        'inspections' => [InspectionsDto::class, 'saveInspections', 'getInspections'],
        'bank' => [BankDto::class, 'saveBank', 'getBank'],
    ];

    public function __construct(private AistsoftDemoRepository $repository)
    {
    }

    public function dispatch(string $method, string $requestUri, string $body = ''): array
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST'], true)) {
            return ['status' => 405, 'body' => ['error' => 'Поддерживаются только методы GET и POST']];
        }

        $path = parse_url($requestUri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim($path ?: '', '/'))));
        if (count($segments) < 2 || $segments[0] !== 'app' || !isset(self::RESOURCE_METHODS[$segments[1]])) {
            return ['status' => 404, 'body' => ['error' => 'Неизвестный ресурс API']];
        }

        [$dtoClass, $saveMethod, $getMethod] = self::RESOURCE_METHODS[$segments[1]];

        try {
            if ($method === 'GET') {
                if (count($segments) !== 3 || !ctype_digit($segments[2]) || (int) $segments[2] < 1) {
                    return ['status' => 400, 'body' => ['error' => 'Некорректный идентификатор ресурса']];
                }

                $dto = $this->{$getMethod}((int) $segments[2]);
                return $dto === null
                    ? ['status' => 404, 'body' => ['error' => 'Ресурс не найден']]
                    : ['status' => 200, 'body' => $dto];
            }

            if (count($segments) !== 2) {
                return ['status' => 404, 'body' => ['error' => 'Неизвестный ресурс API']];
            }

            $payload = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
            if (
                !$payload instanceof stdClass
                || !property_exists($payload, 'dto')
                || !$payload->dto instanceof stdClass
            ) {
                return [
                    'status' => 400,
                    'body' => ['error' => 'Запрос не содержит корректный объект DTO'],
                ];
            }

            $requestParameters = [];
            if (isset($payload->requestParameters)) {
                if (!$payload->requestParameters instanceof stdClass) {
                    return [
                        'status' => 400,
                        'body' => ['error' => 'Запрос не содержит корректный объект requestParameters'],
                    ];
                }
                $requestParameters = (array) $payload->requestParameters;
            }

            $dto = $this->hydrateDto($dtoClass, $payload->dto);
            $entity = $this->{$saveMethod}($dto, $requestParameters);

            return ['status' => 201, 'body' => ['id' => $entity->id]];
        } catch (JsonException $exception) {
            return ['status' => 400, 'body' => ['error' => 'Некорректное тело запроса JSON']];
        } catch (InvalidArgumentException $exception) {
            return ['status' => 400, 'body' => ['error' => $exception->getMessage()]];
        } catch (\TypeError $exception) {
            return ['status' => 400, 'body' => ['error' => 'Поля DTO не соответствуют схеме ресурса']];
        } catch (DatabaseException $exception) {
            error_log($exception->getMessage());
            return ['status' => 500, 'body' => ['error' => 'Ошибка при работе с БД']];
        }
    }

    public function run(): void
    {
        $result = $this->dispatch(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            file_get_contents('php://input') ?: ''
        );

        http_response_code($result['status']);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode($result['body'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            error_log($exception->getMessage());
            http_response_code(500);
            echo '{"error":"Ошибка при формировании JSON-ответа"}';
        }
    }

    /**
     * Сохраняет результаты поиска.
     *
     * @param SearchDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return SearchEntity Сохраненная запись.
     */
    public function saveSearch(SearchDto $dto, array $requestParameters = []): SearchEntity
    {
        return $this->saveDto($dto, SearchEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные организации.
     *
     * @param CompanyDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return CompanyEntity Сохраненная запись.
     */
    public function saveCompany(CompanyDto $dto, array $requestParameters = []): CompanyEntity
    {
        return $this->saveDto($dto, CompanyEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные индивидуального предпринимателя.
     *
     * @param EntrepreneurDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return EntrepreneurEntity Сохраненная запись.
     */
    public function saveEntrepreneur(
        EntrepreneurDto $dto,
        array $requestParameters = []
    ): EntrepreneurEntity {
        return $this->saveDto($dto, EntrepreneurEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные физического лица.
     *
     * @param PersonDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return PersonEntity Сохраненная запись.
     */
    public function savePerson(PersonDto $dto, array $requestParameters = []): PersonEntity
    {
        return $this->saveDto($dto, PersonEntity::class, $requestParameters);
    }

    /**
     * Сохраняет финансовые данные.
     *
     * @param FinancesDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return FinancesEntity Сохраненная запись.
     */
    public function saveFinances(FinancesDto $dto, array $requestParameters = []): FinancesEntity
    {
        return $this->saveDto($dto, FinancesEntity::class, $requestParameters);
    }

    /**
     * Сохраняет оценку надежности.
     *
     * @param ReliabilityDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return ReliabilityEntity Сохраненная запись.
     */
    public function saveReliability(
        ReliabilityDto $dto,
        array $requestParameters = []
    ): ReliabilityEntity {
        return $this->saveDto($dto, ReliabilityEntity::class, $requestParameters);
    }

    /**
     * Сохраняет историю событий.
     *
     * @param TimelineDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return TimelineEntity Сохраненная запись.
     */
    public function saveTimeline(TimelineDto $dto, array $requestParameters = []): TimelineEntity
    {
        return $this->saveDto($dto, TimelineEntity::class, $requestParameters);
    }

    /**
     * Сохраняет арбитражные дела.
     *
     * @param LegalCasesDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return LegalCasesEntity Сохраненная запись.
     */
    public function saveLegalCases(
        LegalCasesDto $dto,
        array $requestParameters = []
    ): LegalCasesEntity {
        return $this->saveDto($dto, LegalCasesEntity::class, $requestParameters);
    }

    /**
     * Сохраняет исполнительные производства.
     *
     * @param EnforcementsDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return EnforcementsEntity Сохраненная запись.
     */
    public function saveEnforcements(
        EnforcementsDto $dto,
        array $requestParameters = []
    ): EnforcementsEntity {
        return $this->saveDto($dto, EnforcementsEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные о контрактах.
     *
     * @param ContractsDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return ContractsEntity Сохраненная запись.
     */
    public function saveContracts(
        ContractsDto $dto,
        array $requestParameters = []
    ): ContractsEntity {
        return $this->saveDto($dto, ContractsEntity::class, $requestParameters);
    }

    /**
     * Сохраняет сообщения Федресурса.
     *
     * @param FedresursDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return FedresursEntity Сохраненная запись.
     */
    public function saveFedresurs(FedresursDto $dto, array $requestParameters = []): FedresursEntity
    {
        return $this->saveDto($dto, FedresursEntity::class, $requestParameters);
    }

    /**
     * Сохраняет сообщения о банкротстве.
     *
     * @param BankruptcyMessagesDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return BankruptcyMessagesEntity Сохраненная запись.
     */
    public function saveBankruptcyMessages(
        BankruptcyMessagesDto $dto,
        array $requestParameters = []
    ): BankruptcyMessagesEntity {
        return $this->saveDto($dto, BankruptcyMessagesEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные о проверках.
     *
     * @param InspectionsDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return InspectionsEntity Сохраненная запись.
     */
    public function saveInspections(
        InspectionsDto $dto,
        array $requestParameters = []
    ): InspectionsEntity {
        return $this->saveDto($dto, InspectionsEntity::class, $requestParameters);
    }

    /**
     * Сохраняет данные банка.
     *
     * @param BankDto $dto Данные для сохранения.
     * @param array $requestParameters Параметры запроса.
     * @return BankEntity Сохраненная запись.
     */
    public function saveBank(BankDto $dto, array $requestParameters = []): BankEntity
    {
        return $this->saveDto($dto, BankEntity::class, $requestParameters);
    }


    /**
     * Получает сохраненные результаты поиска.
     *
     * @param int $id Идентификатор записи.
     * @return SearchDto|null DTO или null, если запись не найдена.
     */
    public function getSearch(int $id): ?SearchDto
    {
        return $this->getDto($id, SearchEntity::class, SearchDto::class);
    }

    /**
     * Получает сохраненные данные организации.
     *
     * @param int $id Идентификатор записи.
     * @return CompanyDto|null DTO или null, если запись не найдена.
     */
    public function getCompany(int $id): ?CompanyDto
    {
        return $this->getDto($id, CompanyEntity::class, CompanyDto::class);
    }

    /**
     * Получает сохраненные данные индивидуального предпринимателя.
     *
     * @param int $id Идентификатор записи.
     * @return EntrepreneurDto|null DTO или null, если запись не найдена.
     */
    public function getEntrepreneur(int $id): ?EntrepreneurDto
    {
        return $this->getDto($id, EntrepreneurEntity::class, EntrepreneurDto::class);
    }

    /**
     * Получает сохраненные данные физического лица.
     *
     * @param int $id Идентификатор записи.
     * @return PersonDto|null DTO или null, если запись не найдена.
     */
    public function getPerson(int $id): ?PersonDto
    {
        return $this->getDto($id, PersonEntity::class, PersonDto::class);
    }

    /**
     * Получает сохраненные финансовые данные.
     *
     * @param int $id Идентификатор записи.
     * @return FinancesDto|null DTO или null, если запись не найдена.
     */
    public function getFinances(int $id): ?FinancesDto
    {
        return $this->getDto($id, FinancesEntity::class, FinancesDto::class);
    }

    /**
     * Получает сохраненную оценку надежности.
     *
     * @param int $id Идентификатор записи.
     * @return ReliabilityDto|null DTO или null, если запись не найдена.
     */
    public function getReliability(int $id): ?ReliabilityDto
    {
        return $this->getDto($id, ReliabilityEntity::class, ReliabilityDto::class);
    }

    /**
     * Получает сохраненную историю событий.
     *
     * @param int $id Идентификатор записи.
     * @return TimelineDto|null DTO или null, если запись не найдена.
     */
    public function getTimeline(int $id): ?TimelineDto
    {
        return $this->getDto($id, TimelineEntity::class, TimelineDto::class);
    }

    /**
     * Получает сохраненные арбитражные дела.
     *
     * @param int $id Идентификатор записи.
     * @return LegalCasesDto|null DTO или null, если запись не найдена.
     */
    public function getLegalCases(int $id): ?LegalCasesDto
    {
        return $this->getDto($id, LegalCasesEntity::class, LegalCasesDto::class);
    }

    /**
     * Получает сохраненные исполнительные производства.
     *
     * @param int $id Идентификатор записи.
     * @return EnforcementsDto|null DTO или null, если запись не найдена.
     */
    public function getEnforcements(int $id): ?EnforcementsDto
    {
        return $this->getDto($id, EnforcementsEntity::class, EnforcementsDto::class);
    }

    /**
     * Получает сохраненные данные о контрактах.
     *
     * @param int $id Идентификатор записи.
     * @return ContractsDto|null DTO или null, если запись не найдена.
     */
    public function getContracts(int $id): ?ContractsDto
    {
        return $this->getDto($id, ContractsEntity::class, ContractsDto::class);
    }

    /**
     * Получает сохраненные сообщения Федресурса.
     *
     * @param int $id Идентификатор записи.
     * @return FedresursDto|null DTO или null, если запись не найдена.
     */
    public function getFedresurs(int $id): ?FedresursDto
    {
        return $this->getDto($id, FedresursEntity::class, FedresursDto::class);
    }

    /**
     * Получает сохраненные сообщения о банкротстве.
     *
     * @param int $id Идентификатор записи.
     * @return BankruptcyMessagesDto|null DTO или null, если запись не найдена.
     */
    public function getBankruptcyMessages(int $id): ?BankruptcyMessagesDto
    {
        return $this->getDto($id, BankruptcyMessagesEntity::class, BankruptcyMessagesDto::class);
    }

    /**
     * Получает сохраненные данные о проверках.
     *
     * @param int $id Идентификатор записи.
     * @return InspectionsDto|null DTO или null, если запись не найдена.
     */
    public function getInspections(int $id): ?InspectionsDto
    {
        return $this->getDto($id, InspectionsEntity::class, InspectionsDto::class);
    }

    /**
     * Получает сохраненные данные банка.
     *
     * @param int $id Идентификатор записи.
     * @return BankDto|null DTO или null, если запись не найдена.
     */
    public function getBank(int $id): ?BankDto
    {
        return $this->getDto($id, BankEntity::class, BankDto::class);
    }


    private function getDto(int $id, string $entityClass, string $dtoClass): ?object
    {
        $entity = $this->repository->find($entityClass, $id);
        if ($entity === null) {
            return null;
        }

        $dto = new $dtoClass();

        try {
            foreach ($entityClass::columns() as $columnName => $definition) {
                $value = $entity->values[$columnName] ?? null;
                if ($value === null) {
                    continue;
                }

                if (str_ends_with($columnName, '_json')) {
                    $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                }

                $this->setDtoPath($dto, $this->dtoPath($entityClass::tableName(), $columnName), $value);
            }
        } catch (JsonException $exception) {
            throw new DatabaseException('Не удалось преобразовать сущность БД в DTO', 0, $exception);
        }

        return $dto;
    }

    private function saveDto(object $dto, string $entityClass, array $requestParameters): Entity
    {
        $entity = new $entityClass();
        $entity->requestParameters = $requestParameters;
        $entity->fetchedAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM);

        try {
            foreach ($entityClass::columns() as $columnName => $definition) {
                $value = $this->readDtoPath($dto, $this->dtoPath($entityClass::tableName(), $columnName));
                if ($value instanceof stdClass || is_array($value)) {
                    $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                } elseif (is_bool($value)) {
                    $value = (int) $value;
                }

                $entity->values[$columnName] = $value;
            }
        } catch (JsonException $exception) {
            throw new DatabaseException('Не удалось преобразовать DTO в сущность БД', 0, $exception);
        }

        return $this->repository->save($entity);
    }

    private function dtoPath(string $tableName, string $columnName): string
    {
        if (str_starts_with($columnName, 'meta_')) {
            return 'meta.' . substr($columnName, 5);
        }
        if (str_starts_with($columnName, 'company_')) {
            return 'company.' . substr($columnName, 8);
        }
        if (str_starts_with($columnName, 'entrepreneur_')) {
            return 'entrepreneur.' . substr($columnName, 13);
        }
        if (str_starts_with($columnName, 'person_')) {
            return 'person.' . substr($columnName, 7);
        }
        if ($columnName === 'source_data_json') {
            return 'source_data';
        }
        if ($columnName === 'bo_nalog_report_id') {
            return 'bo_nalog_ru.id';
        }
        if ($columnName === 'bo_nalog_reports_json') {
            return 'bo_nalog_ru.reports';
        }
        if ($tableName === 'timeline' && $columnName === 'events_json') {
            return 'data';
        }

        $property = preg_replace('/_json$/', '', $columnName);
        if (str_starts_with($property, 'data_')) {
            $property = substr($property, 5);
        }

        return 'data.' . $property;
    }

    private function readDtoPath(object $dto, string $path): mixed
    {
        $value = $dto;
        foreach (explode('.', $path) as $segment) {
            if (!is_object($value) || !property_exists($value, $segment)) {
                return null;
            }

            $value = $value->{$segment};
        }

        return $value;
    }

    private function hydrateDto(string $dtoClass, stdClass $data): object
    {
        $dto = new $dtoClass();

        foreach (get_object_vars($data) as $name => $value) {
            if (!property_exists($dto, $name)) {
                throw new InvalidArgumentException(sprintf('Неизвестное поле DTO: %s', $name));
            }

            $type = (new ReflectionProperty($dto, $name))->getType();
            if ($value instanceof stdClass && $type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $value = $this->hydrateDto($type->getName(), $value);
            } elseif ($value instanceof stdClass && $type instanceof ReflectionNamedType && $type->getName() === 'array') {
                $value = (array) $value;
            }

            $dto->{$name} = $value;
        }

        return $dto;
    }

    private function setDtoPath(object $dto, string $path, mixed $value): void
    {
        $segments = explode('.', $path);
        $property = array_pop($segments);
        $target = $dto;

        foreach ($segments as $segment) {
            $nested = $target->{$segment} ?? null;
            if ($nested === null) {
                $type = (new ReflectionProperty($target, $segment))->getType();
                if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    throw new DatabaseException('Не удалось определить вложенный DTO');
                }

                $nested = new ($type->getName())();
                $target->{$segment} = $nested;
            }

            $target = $nested;
        }

        $type = (new ReflectionProperty($target, $property))->getType();
        if ($type instanceof ReflectionNamedType && $value !== null) {
            $value = match ($type->getName()) {
                'bool' => (bool) $value,
                'float' => (float) $value,
                'int' => (int) $value,
                'string' => (string) $value,
                default => $value,
            };
        }

        $target->{$property} = $value;
    }
}
