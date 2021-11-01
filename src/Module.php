<?php

namespace kibrycker\api\rasp;

use Yii;
use yii\base\Application;
use yii\i18n\PhpMessageSource;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;

/**
 * Модуль для получения данных по расписанию
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /** @var string Урл запроса для получения расписания */
    const URL_REQUEST = 'https://api.rasp.yandex.net';

    /** @var string Версия запроса для получения расписания */
    const VERSION_URL_REQUEST = 'v3.0';

    /** @var float|int Время жизни кэша по-умолчанию */
    const DURATION_CACHE = 3600 * 24;

    /** @var string API Yandex-ключ, полученный в кабинете Разработчика */
    public string $apiYandexKey;

    /** @var string Собранный урл для получения расписания */
    public string $apiUrl;

    /** @var float|int Время жизни кэша */
    public int $duration = self::DURATION_CACHE;

    /**
     * Инициализация модуля
     */
    public function init(): void
    {
        parent::init();
        $this->registerTranslations();
        $this->apiUrl = self::URL_REQUEST . '/' . self::VERSION_URL_REQUEST;
    }

    /**
     * Загрузчик модуля
     *
     * @param Application $app Текущее приложение
     * @throws InvalidConfigException
     */
    public function bootstrap($app): void
    {
        $this->registerTranslations();
    }

    /**
     * Логирование сообщений отладки
     * @param string $type Тип сообщения: debug, info, warning, error
     * @param string $message Текст сообщения отладки
     * @param string|null $category Категория сообщения отладки. Если =null, то берется по имени модуля ($this->id)
     */
    public static function log(string $type, string $message, string $category = null): void
    {
        if (!in_array($type, ['debug', 'info', 'warning', 'error'])) {
            throw new InvalidArgumentException(static::t('errors', 'Invalid log message type'));
        }
        Yii::$type($message, $category ?? Module::getInstance()->id);
    }

    /**
     * Получение экземпляра модуля
     * @return Module|null
     */
    public static function getInstance(): ?Module
    {
        $module = parent::getInstance();
        if ($module === null) {
            throw new \RuntimeException(
                static::t('errors', 'Failed to instantiate `{obj}` object', ['obj' => static::class])
            );
        }
        return $module;
    }

    /**
     * Добавление переводов для модуля в систему
     */
    private function registerTranslations(): void
    {
        if (!empty(Yii::$app->i18n->translations[$this->id . '*'])) {
            /** переводы уже зарегистрированы */
            return;
        }
        $fileMap = [];
        foreach (['main', 'errors'] as $name) {
            $fileMap[$this->id . '/' . $name] = $name . '.php';
        }
        Yii::$app->i18n->translations[$this->id . '*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
            'fileMap' => $fileMap,
        ];
    }

    /**
     * Метод получения перевода строки
     * @param string $category Категория строки для перевода
     * @param string $message Исходная строка для перевода
     * @param array $params Параметры используемые для замены соответствующих плейсхолдеров в строке
     * @param null $language Код языка перевода (например: `en-US`, `en`). Если null, то текущий язык системы
     * @return string
     */
    public static function t(string $category, string $message, array $params = [], $language = null): string
    {
        return Yii::t(Module::getInstance()->id . '/' . $category, $message, $params, $language);
    }
}