<?php

namespace Drupal\current_weather\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
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
   * AccEngage constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
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
    $logger = \Drupal::logger('locale');

    if (!empty($city)) {
      $query_data = $city;
    }

    if (!empty($country)) {
      $query_data .= !empty($city) ? ', ' . $country : $country;
    }

    $client = \Drupal::service('http_client_factory')->fromOptions([
      'base_uri' => static::API_URL,
    ]);

    if (!empty($query_data)) {
      try {
        $response = $client->get($this->getDefaultEndpoint(), [
          'query' => [
            'q' => $query_data,
            'units' => 'metric',
            'appid' => $this->getAppKey(),
          ],
        ]);
      }
      catch (RequestException $e) {
        // Handle 4xx and 5xx http responses.
        if ($response = $e->getResponse()) {
          if ($response->getStatusCode() == 404) {
            \Drupal::messenger()
              ->addMessage('Location not found: ' . $query_data, 'warning');
            return [];
          }
          $logger->notice('HTTP request to @url failed with error: @error.', [
            '@url' => $query_data,
            '@error' => $response->getStatusCode() . ' ' . $response->getReasonPhrase(),
          ]);
        }
      }
    }
    else {
      return [];
    }

    return Json::decode($response->getBody());
  }

}
