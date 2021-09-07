<?php

namespace Drupal\current_weather\Controller;

/**
 * Create weather page.
 */
class WeatherWatcher {

  /**
   * The configuration factory.
   *
   * @var ApiConnector
   */
  protected $api;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->api = \Drupal::service('current_weather.weather.apiclient');
  }

  /**
   * Get data from API.
   */
  public function get() {
    $cat_facts = $this->api->connect();
    $items = self::createWeatherData($cat_facts);

    return [
      '#theme' => 'current_weather_template',
      '#items' => $items,
    ];

  }

  /**
   * Get data from API.
   */
  public function getForCity($city) {
    $cat_facts = $this->api->connect($city);
    if (is_array($cat_facts)) {
      $items = self::createWeatherData($cat_facts);
    }
    else {
      return [
        '#markup' => $cat_facts,
      ];
    }

    return [
      '#theme' => 'current_weather_template',
      '#items' => $items,
    ];
  }

  /**
   * Get data from API.
   */
  public function getForCityCountry($city, $country) {
    $cat_facts = $this->api->connect($city, $country);
    if (is_array($cat_facts)) {
      $items = self::createWeatherData($cat_facts);
    }
    else {
      return [
        '#markup' => $cat_facts,
      ];
    }

    return [
      '#theme' => 'current_weather_template',
      '#items' => $items,
    ];
  }

  /**
   * @param $cat_facts
   *
   * @return array
   */
  public static function createWeatherData($cat_facts) {
    if (!empty($cat_facts)) {
      $items['flag'] = '<img src="https://openweathermap.org/images/flags/' . strtolower($cat_facts['sys']['country']) . '.png">';
      $items['icon'] = '<img src="https://openweathermap.org/img/wn/' . strtolower($cat_facts['weather'][0]['icon']) . '.png">';
      $items['name'] = $cat_facts['name'];
      $items['country'] = $cat_facts['sys']['country'];
      $items['main'] = $cat_facts['weather'][0]['main'];
      $items['description'] = $cat_facts['weather'][0]['description'];
      $items['temp'] = $cat_facts['main']['temp'];
      $items['feels_like'] = $cat_facts['main']['feels_like'];
      $items['humidity'] = $cat_facts['main']['humidity'];
      $items['pressure'] = $cat_facts['main']['pressure'];
    }
    else {
      return t('Please, input correct location for weather.');
    }

    return $items;
  }

}
