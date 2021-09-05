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
      '#markup' => $items,
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
      '#markup' => $items,
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
      '#markup' => $items,
    ];
  }

  /**
   * @param $cat_facts
   *
   * @return string
   */
  public static function createWeatherData($cat_facts) {
    if (!empty($cat_facts)) {
      $flag = '<img src="https://openweathermap.org/images/flags/' . strtolower($cat_facts['sys']['country']) . '.png">';
      $icon = '<img src="https://openweathermap.org/img/wn/' . strtolower($cat_facts['weather'][0]['icon']) . '.png">';
      $items = '<div class="weather-wrapper">';
      $items .= '<h2>Weather for ' . $cat_facts['name'] . '</h2>';
      $items .= '<div>' . $cat_facts['name'] . ' (' . $cat_facts['sys']['country'] . ' ' . $flag . ')</div>';
      $items .= '<div>' . $cat_facts['weather'][0]['main'] . ' (' . $cat_facts['weather'][0]['description'] . ')</div>';
      $items .= '<div>' . $icon . '</div>';
      $items .= '<div>' . $cat_facts['main']['temp'] . 'ºC</div>';
      $items .= '<div>' . $cat_facts['main']['feels_like'] . 'ºC</div>';
      $items .= '<div>' . $cat_facts['main']['humidity'] . '%</div>';
      $items .= '<div>' . $cat_facts['main']['pressure'] . 'hPa</div>';
      $items .= '</div>';
    }
    else {
      return t('Please, input correct location for weather.');
    }

    return $items;
  }

}
