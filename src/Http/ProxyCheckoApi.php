<?php

declare(strict_types=1);

namespace AistsoftDemo\Http;

use AistsoftDemo\Client\CheckoClient;
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
use AistsoftDemo\Http\Parameter\ContractsLaw;
use AistsoftDemo\Http\Parameter\ContractsRole;
use AistsoftDemo\Http\Parameter\ContractsSort;
use AistsoftDemo\Http\Parameter\DateSort;
use AistsoftDemo\Http\Parameter\FedresursRole;
use AistsoftDemo\Http\Parameter\LegalCasesRole;
use AistsoftDemo\Http\Parameter\SearchBy;
use AistsoftDemo\Http\Parameter\SearchCodes;
use AistsoftDemo\Http\Parameter\SearchObject;
use InvalidArgumentException;
use JsonException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use stdClass;

class ProxyCheckoApi
{
    // Список поддерживаемых ресурсов Checko API и соответствующих DTO
    private const RESOURCE_METHODS = [
        'search' => ['search', SearchDto::class],
        'company' => ['company', CompanyDto::class],
        'entrepreneur' => ['entrepreneur', EntrepreneurDto::class],
        'person' => ['person', PersonDto::class],
        'finances' => ['finances', FinancesDto::class],
        'reliability' => ['reliability', ReliabilityDto::class],
        'timeline' => ['timeline', TimelineDto::class],
        'legal-cases' => ['legalCases', LegalCasesDto::class],
        'enforcements' => ['enforcements', EnforcementsDto::class],
        'contracts' => ['contracts', ContractsDto::class],
        'fedresurs' => ['fedresurs', FedresursDto::class],
        'bankruptcy-messages' => ['bankruptcyMessages', BankruptcyMessagesDto::class],
        'inspections' => ['inspections', InspectionsDto::class],
        'bank' => ['bank', BankDto::class],
    ];

    public function __construct(private CheckoClient $checkoClient)
    {
    }

    /**
     * Найти организации или индивидуальных предпринимателей по заданным условиям.
     *
     * @param SearchBy $by Тип поиска: наименование, ФИО, учредитель, руководитель,
     *                     ОКВЭД, даты регистрации/обновления, телефон или email.
     * @param SearchObject $obj Область поиска: организации или индивидуальные предприниматели.
     * @param string $query Текст запроса; для наименования и ФИО минимум 4 символа.
     *                      Даты задаются в формате YYYY-MM-DD.
     * @param string|null $region Двухзначный код региона.
     * @param string|null $okved Код основного вида деятельности по ОКВЭД-2.
     * @param string|null $opf Двух- или пятизначный код организационно-правовой формы по ОКОПФ.
     * @param bool|null $active При true в результатах остаются только действующие организации и ИП.
     * @param SearchCodes|null $codes Значение all включает дополнительные коды ОКВЭД при поиске по ОКВЭД.
     * @param int|null $limit Количество результатов на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     */
    public function search(
        SearchBy $by,
        SearchObject $obj,
        string $query,
        ?string $region = null,
        ?string $okved = null,
        ?string $opf = null,
        ?bool $active = null,
        ?SearchCodes $codes = null,
        ?int $limit = null,
        ?int $page = null
    ): SearchDto {
        return $this->request('search', ...get_defined_vars());
    }

    /**
     * Получить сведения об организации из ЕГРЮЛ.
     *
     * @param string $ogrn ОГРН организации.
     * @param string $inn ИНН организации; укажите его, если ОГРН не задан.
     * @param string $okpo Код ОКПО организации.
     * @param bool|null $source При true добавляются исходные данные ЕГРЮЛ.
     */
    public function company(
        string $ogrn = '',
        string $inn = '',
        string $okpo = '',
        ?bool $source = null
    ): CompanyDto {
        return $this->request('company', ...get_defined_vars());
    }

    /**
     * Получить сведения об индивидуальном предпринимателе из ЕГРИП.
     *
     * @param string $ogrn ОГРНИП предпринимателя.
     * @param string $inn ИНН предпринимателя; укажите его, если ОГРНИП не задан.
     * @param string $okpo Код ОКПО предпринимателя.
     * @param bool|null $source При true добавляются исходные данные ЕГРИП.
     */
    public function entrepreneur(
        string $ogrn = '',
        string $inn = '',
        string $okpo = '',
        ?bool $source = null
    ): EntrepreneurDto {
        return $this->request('entrepreneur', ...get_defined_vars());
    }

    /**
     * Получить сведения о физическом лице и его связях по ИНН.
     *
     * @param string $inn ИНН физического лица.
     */
    public function person(string $inn): PersonDto
    {
        return $this->request('person', ...get_defined_vars());
    }

    /**
     * Получить бухгалтерскую (финансовую) отчетность организации.
     *
     * @param string $ogrn ОГРН организации.
     * @param string $inn ИНН организации; укажите его, если ОГРН не задан.
     * @param bool|null $extended При true возвращается расширенная версия финансовой отчетности.
     */
    public function finances(
        string $ogrn = '',
        string $inn = '',
        ?bool $extended = null
    ): FinancesDto {
        return $this->request('finances', ...get_defined_vars());
    }

    /**
     * Получить оценку надежности организации или предпринимателя.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     */
    public function reliability(
        string $ogrn = '',
        string $inn = ''
    ): ReliabilityDto {
        return $this->request('reliability', ...get_defined_vars());
    }

    /**
     * Получить историю событий и изменений организации или предпринимателя.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     */
    public function timeline(
        string $ogrn = '',
        string $inn = ''
    ): TimelineDto {
        return $this->request('timeline', ...get_defined_vars());
    }

    /**
     * Получить арбитражные дела организации или индивидуального предпринимателя.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param LegalCasesRole|null $role Роль в процессе: истец или ответчик.
     * @param bool|null $actual При true исключаются дела с возвращенными или отклоненными исками,
     *                          прекращенными производствами и другими неактуальными статусами.
     * @param bool|null $active При true возвращаются только незавершенные дела.
     * @param string|null $date_from Начало периода возбуждения дела в формате YYYY-MM-DD.
     * @param string|null $date_to Конец периода возбуждения дела в формате YYYY-MM-DD.
     * @param float|null $claim_amount_from Минимальная сумма исковых требований.
     * @param float|null $claim_amount_to Максимальная сумма исковых требований.
     * @param int|null $limit Количество дел на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param DateSort|null $sort Сортировка по дате возбуждения дела: по возрастанию или убыванию.
     */
    public function legalCases(
        string $ogrn = '',
        string $inn = '',
        ?LegalCasesRole $role = null,
        ?bool $actual = null,
        ?bool $active = null,
        ?string $date_from = null,
        ?string $date_to = null,
        ?float $claim_amount_from = null,
        ?float $claim_amount_to = null,
        ?int $limit = null,
        ?int $page = null,
        ?DateSort $sort = null
    ): LegalCasesDto {
        return $this->request('legal-cases', ...get_defined_vars());
    }

    /**
     * Получить исполнительные производства ФССП по организации или предпринимателю.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param int|null $limit Количество производств на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param DateSort|null $sort Сортировка по дате возбуждения производства: по возрастанию или убыванию.
     */
    public function enforcements(
        string $ogrn = '',
        string $inn = '',
        ?int $limit = null,
        ?int $page = null,
        ?DateSort $sort = null
    ): EnforcementsDto {
        return $this->request('enforcements', ...get_defined_vars());
    }

    /**
     * Получить сведения о контрактах по 44-ФЗ, 94-ФЗ или 223-ФЗ.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param ContractsLaw|null $law Закон о закупках: 44-ФЗ, 94-ФЗ или 223-ФЗ.
     * @param ContractsRole|null $role Роль в контракте: заказчик или поставщик.
     * @param int|null $limit Количество контрактов на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param ContractsSort|null $sort Сортировка по дате подписания или цене, по возрастанию/убыванию.
     */
    public function contracts(
        string $ogrn = '',
        string $inn = '',
        ?ContractsLaw $law = null,
        ?ContractsRole $role = null,
        ?int $limit = null,
        ?int $page = null,
        ?ContractsSort $sort = null
    ): ContractsDto {
        return $this->request('contracts', ...get_defined_vars());
    }

    /**
     * Получить сообщения ЕФРСФДЮЛ о компании или предпринимателе.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param string|null $type Тип сообщения из справочника Checko.
     * @param FedresursRole|null $role Роль в сообщении: все сообщения с участием субъекта либо только опубликованные им.
     * @param string|null $date_from Начало периода публикации в формате YYYY-MM-DD.
     * @param string|null $date_to Конец периода публикации в формате YYYY-MM-DD.
     * @param int|null $limit Количество сообщений на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param DateSort|null $sort Сортировка по дате публикации: по возрастанию или убыванию.
     */
    public function fedresurs(
        string $ogrn = '',
        string $inn = '',
        ?string $type = null,
        ?FedresursRole $role = null,
        ?string $date_from = null,
        ?string $date_to = null,
        ?int $limit = null,
        ?int $page = null,
        ?DateSort $sort = null
    ): FedresursDto {
        return $this->request('fedresurs', ...get_defined_vars());
    }

    /**
     * Получить сообщения из реестра банкротств по организации или предпринимателю.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param string|null $date_from Начало периода публикации в формате YYYY-MM-DD.
     * @param string|null $date_to Конец периода публикации в формате YYYY-MM-DD.
     * @param int|null $limit Количество сообщений на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param DateSort|null $sort Сортировка по дате публикации: по возрастанию или убыванию.
     */
    public function bankruptcyMessages(
        string $ogrn = '',
        string $inn = '',
        ?string $date_from = null,
        ?string $date_to = null,
        ?int $limit = null,
        ?int $page = null,
        ?DateSort $sort = null
    ): BankruptcyMessagesDto {
        return $this->request('bankruptcy-messages', ...get_defined_vars());
    }

    /**
     * Получить сведения о проверках организации или предпринимателя.
     *
     * @param string $ogrn ОГРН организации или ОГРНИП предпринимателя.
     * @param string $inn ИНН организации или предпринимателя; укажите ИНН, если ОГРН/ОГРНИП не задан.
     * @param int|null $limit Количество проверок на страницу: по умолчанию 100, максимум 100.
     * @param int|null $page Номер страницы результатов.
     * @param DateSort|null $sort Сортировка по дате начала проверки: по возрастанию или убыванию.
     */
    public function inspections(
        string $ogrn = '',
        string $inn = '',
        ?int $limit = null,
        ?int $page = null,
        ?DateSort $sort = null
    ): InspectionsDto {
        return $this->request('inspections', ...get_defined_vars());
    }

    /**
     * Получить реквизиты банка или кредитной организации по БИК.
     *
     * @param string $bic БИК банка.
     */
    public function bank(string $bic): BankDto
    {
        return $this->request('bank', ...get_defined_vars());
    }


    public function dispatch(string $method, string $requestUri, string $queryString): array
    {
        if (strtoupper($method) !== 'GET') {
            return [
                'status' => 405,
                'body' => ['error' => 'Поддерживается только метод GET'],
            ];
        }

        parse_str($queryString, $query);
        $path = parse_url($requestUri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim($path ?: '', '/'))));
        if (count($segments) !== 2 || $segments[0] !== 'checko') {
            return [
                'status' => 404,
                'body' => ['error' => 'Ресурс Checko API не найден'],
            ];
        }

        $resource = $segments[1];

        if ($resource === '') {
            return [
                'status' => 404,
                'body' => ['error' => 'Ресурс Checko API не указан'],
            ];
        }

        if (!isset(self::RESOURCE_METHODS[$resource]) || !$this->checkoClient->supports($resource)) {
            return [
                'status' => 404,
                'body' => ['error' => sprintf('Ресурс %s Checko API не поддерживается', $resource)],
            ];
        }

        unset($query['resource'], $query['key']);

        try {
            [$resourceMethod] = self::RESOURCE_METHODS[$resource];
            $method = new ReflectionMethod($this, $resourceMethod);
            $arguments = [];
            foreach ($method->getParameters() as $parameter) {
                $name = $parameter->getName();
                if (array_key_exists($name, $query)) {
                    $arguments[$name] = $this->castQueryParameter($query[$name], $parameter);
                }
            }

            return [
                'status' => 200,
                'body' => $this->{$resourceMethod}(...$arguments),
            ];
        } catch (InvalidArgumentException $exception) {
            return [
                'status' => 400,
                'body' => ['error' => $exception->getMessage()],
            ];
        } catch (RuntimeException $exception) {
            error_log($exception->getMessage());

            return [
                'status' => 502,
                'body' => ['error' => 'Ошибка при обращении к Checko API'],
            ];
        } catch (\TypeError $exception) {
            return [
                'status' => 400,
                'body' => ['error' => 'Недопустимое значение параметра запроса'],
            ];
        }
    }

    public function run(): void
    {
        $result = $this->dispatch(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            http_build_query($_GET)
        );

        http_response_code($result['status']);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode($result['body'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            error_log($exception->getMessage());
            http_response_code(500);
            echo '{"error":"Ошибка при кодировании ответа Checko API"}';
        }
    }

    private function request(string $resource, string|int|float|bool|\BackedEnum|null ...$parameters): object
    {
        foreach ($parameters as $name => $value) {
            if ($value === null || $value === '') {
                unset($parameters[$name]);
                continue;
            }

            $parameters[$name] = match (true) {
                $value instanceof \BackedEnum => (string) $value->value,
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };
        }

        $response = $this->checkoClient->get($resource, $parameters);
        [, $dtoClass] = self::RESOURCE_METHODS[$resource];

        return $this->makeDto($dtoClass, $response);
    }

    private function castQueryParameter(mixed $value, \ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        if (!$type instanceof ReflectionNamedType || !is_string($value)) {
            throw new InvalidArgumentException(sprintf('Недопустимое значение параметра %s', $parameter->getName()));
        }

        if (!$type->isBuiltin()) {
            $enumClass = $type->getName();
            if (enum_exists($enumClass) && is_subclass_of($enumClass, \BackedEnum::class)) {
                $enumValue = $enumClass::tryFrom($value);
                if ($enumValue !== null) {
                    return $enumValue;
                }
            }

            throw new InvalidArgumentException(sprintf('Недопустимое значение параметра %s', $parameter->getName()));
        }

        return match ($type->getName()) {
            'string' => $value,
            'int' => filter_var($value, FILTER_VALIDATE_INT) !== false
                ? (int) $value
                : throw new InvalidArgumentException(sprintf('Параметр %s должен быть целым числом', $parameter->getName())),
            'float' => is_numeric($value)
                ? (float) $value
                : throw new InvalidArgumentException(sprintf('Параметр %s должен быть числом', $parameter->getName())),
            'bool' => match (strtolower($value)) {
                'true' => true,
                'false' => false,
                default => throw new InvalidArgumentException(
                    sprintf('Параметр %s должен иметь значение true или false', $parameter->getName())
                ),
            },
            default => throw new InvalidArgumentException(sprintf('Недопустимый тип параметра %s', $parameter->getName())),
        };
    }

    private function makeDto(string $dtoClass, stdClass $data): object
    {
        $dto = new $dtoClass();

        foreach (get_object_vars($data) as $name => $value) {
            if (!property_exists($dto, $name)) {
                continue;
            }

            $propertyType = (new ReflectionProperty($dto, $name))->getType();
            if (!$propertyType instanceof ReflectionNamedType) {
                throw new RuntimeException(sprintf('Неподдерживаемый тип свойства DTO для %s', $name));
            }

            if ($value === null && !$propertyType->allowsNull()) {
                continue;
            }

            $dto->{$name} = $this->makeValue($value, $propertyType);
        }

        return $dto;
    }

    private function makeValue(mixed $value, ReflectionNamedType $type): mixed
    {
        if ($value === null) {
            return null;
        }

        $typeName = $type->getName();
        if ($typeName === 'array') {
            if (!is_array($value) && !$value instanceof stdClass) {
                throw new RuntimeException('Checko API вернул значение, которое не соответствует типу массива DTO');
            }

            return $this->toArray($value);
        }

        if (!$type->isBuiltin()) {
            if (!$value instanceof stdClass) {
                throw new RuntimeException('Checko API вернул значение, которое не соответствует типу вложенного DTO');
            }

            return $this->makeDto($typeName, $value);
        }

        if ($typeName === 'string' && is_scalar($value)) {
            return (string) $value;
        }
        if ($typeName === 'int' && (is_int($value) || (is_string($value) && preg_match('/^-?\d+$/', $value)))) {
            return (int) $value;
        }
        if ($typeName === 'float' && is_numeric($value)) {
            return (float) $value;
        }
        if ($typeName === 'bool' && is_bool($value)) {
            return $value;
        }

        throw new RuntimeException(sprintf('Checko API вернул значение, которое не соответствует типу DTO %s', $typeName));
    }

    private function toArray(array|stdClass $value): array
    {
        $array = $value instanceof stdClass ? get_object_vars($value) : $value;
        foreach ($array as $key => $item) {
            if ($item instanceof stdClass || is_array($item)) {
                $array[$key] = $this->toArray($item);
            }
        }

        return $array;
    }
}
