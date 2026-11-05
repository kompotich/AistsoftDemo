const searchForm = document.querySelector('#search-form');
    const searchButton = searchForm.querySelector('button');
    const searchMessage = document.querySelector('#search-message');
    const searchResults = document.querySelector('#search-results');
    const detailsForm = document.querySelector('#details-form');
    const detailsButton = detailsForm.querySelector('button');
    const detailsMessage = document.querySelector('#details-message');
    const recordCard = document.querySelector('#record-card');
    const recordTitle = document.querySelector('#record-title');
    const recordJson = document.querySelector('#record-json');
    const saveButton = document.querySelector('#save-button');
    const saveMessage = document.querySelector('#save-message');
    let currentRecord = null;
    let currentResource = null;
    let currentRequestParameters = {};

    function setMessage(element, message, isError = false) {
        element.textContent = message;
        element.classList.toggle('error', isError);
    }

    async function requestJson(url, options) {
        const response = await fetch(url, options);
        let payload;
        try {
            payload = await response.json();
        } catch {
            throw new Error('Сервер вернул ответ, который не удалось прочитать.');
        }

        if (!response.ok) {
            throw new Error(payload.error || `Ошибка запроса (${response.status}).`);
        }
        return payload;
    }

    function recordValue(record, ...keys) {
        for (const key of keys) {
            if (record && record[key] !== undefined && record[key] !== null && record[key] !== '') {
                return record[key];
            }
        }
        return '';
    }

    function displayValue(value) {
        if (typeof value === 'object' && value !== null) {
            return JSON.stringify(value);
        }
        return String(value);
    }

    async function loadRecord(type, idType, idValue) {
        const resource = type === 'entrepreneur' ? 'entrepreneur' : 'company';
        const params = new URLSearchParams({[idType]: idValue, source: 'true'});
        setMessage(detailsMessage, 'Запрашиваем сведения…');
        detailsButton.disabled = true;
        saveButton.disabled = true;
        recordCard.hidden = true;
        currentRecord = null;
        currentResource = null;
        setMessage(saveMessage, '');

        try {
            const payload = await requestJson(`/checko/${resource}?${params}`);
            currentRecord = payload;
            currentResource = resource;
            currentRequestParameters = {[idType]: idValue};
            recordTitle.textContent = type === 'entrepreneur' ? 'Сведения об ИП' : 'Сведения о компании';
            recordJson.textContent = JSON.stringify(payload, null, 2);
            recordCard.hidden = false;
            setMessage(detailsMessage, 'Сведения загружены.');
            saveButton.disabled = false;
        } catch (error) {
            currentRecord = null;
            currentResource = null;
            setMessage(detailsMessage, error.message, true);
        } finally {
            detailsButton.disabled = false;
        }
    }

    searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const query = document.querySelector('#search-query').value.trim();
        const type = document.querySelector('#search-type').value;
        if (query.length < 4) {
            setMessage(searchMessage, 'Введите не менее 4 символов.', true);
            return;
        }

        const params = new URLSearchParams({by: 'name', obj: type, query, limit: '10'});
        searchButton.disabled = true;
        searchResults.replaceChildren();
        setMessage(searchMessage, 'Ищем…');

        try {
            const payload = await requestJson(`/checko/search?${params}`);
            const records = payload?.data?.records || [];
            if (!Array.isArray(records) || records.length === 0) {
                setMessage(searchMessage, 'Ничего не найдено.');
                return;
            }

            setMessage(searchMessage, `Найдено записей: ${records.length}.`);
            records.forEach((record) => {
                const data = record.data || record;
                const name = recordValue(data, 'full_name', 'short_name', 'name', 'НаимПолн', 'НаимСокр', 'ФИО')
                    || 'Название не указано';
                const inn = recordValue(data, 'inn', 'ИНН');
                const ogrn = recordValue(data, 'ogrn', 'ogrnip', 'ОГРН', 'ОГРНИП');
                const row = document.createElement('article');
                row.className = 'result';
                const text = document.createElement('div');
                const title = document.createElement('strong');
                title.textContent = displayValue(name);
                text.append(title);
                const identifiers = document.createElement('p');
                identifiers.textContent = [
                    inn ? `ИНН: ${displayValue(inn)}` : '',
                    ogrn ? `ОГРН: ${displayValue(ogrn)}` : ''
                ].filter(Boolean).join(' · ') || 'ИНН и ОГРН в результате не указаны';
                text.append(identifiers);
                const openButton = document.createElement('button');
                openButton.type = 'button';
                openButton.className = 'secondary-button';
                openButton.textContent = 'Открыть';
                openButton.disabled = !inn && !ogrn;
                openButton.addEventListener('click', () => {
                    const idType = inn ? 'inn' : 'ogrn';
                    const idValue = inn || ogrn;
                    document.querySelector('#details-type').value = type === 'ent' ? 'entrepreneur' : 'company';
                    document.querySelector('#details-id-type').value = idType;
                    document.querySelector('#details-id').value = idValue;
                    loadRecord(type === 'ent' ? 'entrepreneur' : 'company', idType, idValue);
                });
                row.append(text, openButton);
                searchResults.append(row);
            });
        } catch (error) {
            setMessage(searchMessage, error.message, true);
        } finally {
            searchButton.disabled = false;
        }
    });

    detailsForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const type = document.querySelector('#details-type').value;
        const idType = document.querySelector('#details-id-type').value;
        const idValue = document.querySelector('#details-id').value.trim();
        if (!idValue) {
            setMessage(detailsMessage, 'Введите ИНН или ОГРН.', true);
            return;
        }
        loadRecord(type, idType, idValue);
    });

    saveButton.addEventListener('click', async () => {
        if (!currentRecord || !currentResource) {
            return;
        }

        saveButton.disabled = true;
        setMessage(saveMessage, 'Сохраняем…');
        try {
            const payload = await requestJson(`/app/${currentResource}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    dto: currentRecord,
                    requestParameters: currentRequestParameters
                })
            });
            setMessage(saveMessage, `Сохранено в базу. ID записи: ${payload.id}.`);
        } catch (error) {
            setMessage(saveMessage, error.message, true);
        } finally {
            saveButton.disabled = false;
        }
    });
