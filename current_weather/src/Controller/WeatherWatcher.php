<?php

namespace Drupal\current_weather\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\current_weather\ApiConnector;

/**
 * Create weather page.
 */
class WeatherWatcher extends ControllerBase {

  /**
   * The configuration factory.
   *
   * @var ApiConnector
   */
  protected $api;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('current_weather.weather.apiclient'));
  }

  /**
   * Constructor.
   *
   * @param \Drupal\current_weather\ApiConnector $api
   */
  public function __construct(ApiConnector $api) {
    $this->api = $api;
  }

  /**
   * Get data from API.
   */
  public function get() {
    $cat_facts = $this->api->connect();
    return [
      '#theme' => 'current_weather_template',
      '#items' => self::createWeatherData($cat_facts),
    ];
  }

  /**
   * Get data from API.
   *
   * @param $city
   *
   * @return array
   */
  public function getForCity($city) {
    $cat_facts = $this->api->connect($city);
    return [
      '#theme' => 'current_weather_template',
      '#items' => self::createWeatherData($cat_facts),
    ];
  }

  /**
   * Get data from API.
   *
   * @param $city
   * @param $country
   *
   * @return array
   */
  public function getForCityCountry($city, $country) {
    $cat_facts = $this->api->connect($city, $country);
    return [
      '#theme' => 'current_weather_template',
      '#items' => self::createWeatherData($cat_facts),
    ];
  }

  /**
   * @param $cat_facts
   *
   * @return array|string
   */
  public static function createWeatherData($cat_facts) {
    if (empty($cat_facts)) {
      $items['error'] = t('Please, input correct location for weather.');
    }
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

    return $items;
  }

}
