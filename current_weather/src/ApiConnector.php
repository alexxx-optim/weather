<?php

namespace Drupal\current_weather;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * API connector.
 */
class ApiConnector {

  /**
   * Base API URL.
   */
  const API_URL = 'https://api.openweathermap.org/';

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  protected $httpClientFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Injected cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * AccEngage constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Logger\LoggerChannelFactory $loggerFactory
   * @param \Drupal\Core\Http\ClientFactory $http_client_factory
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   * @param \Drupal\Component\Datetime\TimeInterface $time
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerChannelFactoryInterface $loggerFactory, ClientFactory $http_client_factory, MessengerInterface $messenger, CacheBackendInterface $cache, TimeInterface $time) {
    $this->configFactory = $config_factory;
    $this->logger = $loggerFactory->get('locale');
    $this->httpClientFactory = $http_client_factory;
    $this->messenger = $messenger;
    $this->time = $time;
    $this->cache = $cache;
  }

  /**
   * Get API key.
   */
  protected function getAppKey() {
    return $this->configFactory->get('current_weather.settings')
        ->get('api_key') ?? '';
  }

  /**
   * Get API endpoint.
   */
  public function getDefaultEndpoint() {
    return $this->configFactory->get('current_weather.settings')
        ->get('api_endpoint') ?? '';
  }

  /**
   * Get default city.
   */
  public function getDefaultCity() {
    return $this->configFactory->get('current_weather.settings')
        ->get('default_city_name') ?? '';
  }

  /**
   * Get default country.
   */
  public function getDefaultCountry() {
    return $this->configFactory->get('current_weather.settings')
        ->get('default_country_code') ?? '';
  }

  /**
   * Connect to API.
   */
  public function connect($city = '', $country = '') {
    $query_data = '';
    $city = !empty($city) ? $city : $this->getDefaultCity();
    $country = !empty($country) ? $country : $this->getDefaultCountry();

    if (!empty($city)) {
      $query_data = $city;
    }

    if (!empty($country)) {
      $query_data .= !empty($city) ? ', ' . $country : $country;
    }

    $client = $this->httpClientFactory->fromOptions([
      'base_uri' => static::API_URL,
    ]);

    if (empty($query_data)) {
      return [];
    }
    $data = $this->cache->get($query_data);
    if (!empty($data) && $query_data === $data->data['0']) {
      return $data->data['1'];
    }
    try {
      $response = $client->get($this->getDefaultEndpoint(), [
        'query' => [
          'q' => $query_data,
          'units' => 'metric',
          'appid' => $this->getAppKey(),
        ],
      ]);
      $this->cache->set($query_data, [
        $query_data,
        Json::decode($response->getBody()),
      ], $this->time->getRequestTime() + (86400));
    }
    catch (RequestException $e) {
      // Handle 4xx and 5xx http responses.
      if ($response = $e->getResponse()) {
        if ($response->getStatusCode() == 404) {
          $this->messenger->addMessage('Location not found: ' . $query_data, 'warning');
          return [];
        }
        $this->logger->notice('HTTP request to @url failed with error: @error.', [
          '@url' => $query_data,
          '@error' => $response->getStatusCode() . ' ' . $response->getReasonPhrase(),
        ]);
      }
    }

    return Json::decode($response->getBody());
  }

}
