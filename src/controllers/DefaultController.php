<?php

namespace kibrycker\api\rasp\controllers;

use yii\helpers\Json;
use GuzzleHttp\Client;
use JsonRpc2\Controller;
use yii\caching\FileCache;

/**
 * Контроллер по-умолчанию для получения данных
 */
class DefaultController extends Controller
{
    /** @var string Урл для запроса с нужной папкой */
    public string $url;

    /** @var array Параметры для запроса */
    public array $query = [];

    /** @var array Результат запроса к API */
    public array $resultRequest;

    /**
     * Тестовое действие для проверки модуля
     *
     * @return string
     */
    public function actionTest(): string
    {
        return 'Default:Test';
    }

    /**
     * Получение списка рейсов между станциями, следующих от указанной станции отправления
     * к указанной станции прибытия и информацию по каждому рейсу
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionSearch(): array
    {
        return $this->getAction('search');
    }

    /**
     * Получение списка рейсов, отправляющихся от указанной станции и информацию по каждому рейсу.
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionSchedule(): array
    {
        return $this->getAction('schedule');
    }

    /**
     * Получение списка станций следования нитки по указанному идентификатору нитки,
     * информацию о каждой нитке и о промежуточных станциях нитки
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionThread(): array
    {
        return $this->getAction('thread');
    }

    /**
     * Получение списка станций, находящихся в указанном радиусе от указанной точки
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionNearestStations(): array
    {
        return $this->getAction('nearest_stations');
    }

    /**
     * Получение информации о ближайшем к указанной точке городе
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionNearestSettlement(): array
    {
        return $this->getAction('nearest_settlement');
    }

    /**
     * Получение информации о перевозчике по указанному коду перевозчика
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionCarrier(): array
    {
        return $this->getAction('carrier');
    }

    /**
     * Получить список всех станций
     *
     * @return []|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionStationsList(): array
    {
        return $this->getAction('stations_list');
    }

    /**
     * Общее действие для получения данных
     *
     * @param string $method Метод API, данные которого нужно получить
     *
     * @return array Результат данных
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getAction(string $method): array
    {
        $this->url = "{$this->module->apiUrl}/{$method}";
        $this->request();
        return $this->resultRequest;
    }

    /**
     * Получение параметров и их обработка, чтобы вернуть массив
     *
     * @return array
     */
    public function getRequestParams(): array
    {
        $reqParams = $this->requestObject->params;
        if (empty($reqParams)) {
            $reqParams = new \StdClass();
        }
        return $this->objectToArray($reqParams);
    }

    /**
     * Перевод данных из объекта в массив
     *
     * @param object $data Объект \StdClass, который нужно преобразовать в массив
     *
     * @return array
     */
    public function objectToArray(object $data): array
    {
        if (gettype($data) == 'object') {
            $data = Json::encode($data);
        }

        if (gettype($data) == 'string') {
            $data = Json::decode($data, true);
        }

        return $data;
    }

    /**
     * Запрос данных и кэширование результата
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(): void
    {
        $module = $this->module;
        $params = array_merge([
            'apikey' => $module->apiYandexKey,
        ], $this->getRequestParams());
        $urlQuery = http_build_query($params);
        $url = "{$this->url}?{$urlQuery}";
        $keyCache = md5($url);
        $cache = new FileCache();
        $data = $cache->get($keyCache);
        if (!empty($data)) {
            $this->resultRequest = Json::decode($data, true);
        } else {
            $client = new Client();
            $response = $client->get($url);
            $body = $response->getBody();
            $data = $body->getContents();
            $result = Json::decode($data, true);
            $cache->set($keyCache, $data, $module::DURATION_CACHE);
            $this->resultRequest = $result;
        }
    }
}