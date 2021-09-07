<?php

namespace Drupal\Tests\current_weather\Unit;

use Drupal\Component\Serialization\Json;
use Drupal\current_weather\Controller\WeatherWatcher;
use Drupal\Tests\UnitTestCase;

/**
 * Class CurrentWeatherTest.
 *
 * @package Drupal\Tests\current_weather\Unit
 * @group current_weather
 */
class CurrentWeatherTest extends UnitTestCase {

  /**
   * Test parce API request.
   *
   * @throws \PHPUnit\Framework\ExpectationFailedException
   * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
   */
  public function testApiRequest() {
    $api_body_data_json = '{"coord":{"lon":24.0232,"lat":49.8383},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"base":"stations","main":{"temp":12.21,"feels_like":11.11,"temp_min":11.21,"temp_max":14.73,"pressure":1024,"humidity":62},"visibility":10000,"wind":{"speed":1,"deg":0},"clouds":{"all":0},"dt":1630824034,"sys":{"type":1,"id":8909,"country":"UA","sunrise":1630813515,"sunset":1630861222},"timezone":10800,"id":702550,"name":"Lviv","cod":200}';
    $test_data_response = [
      'flag' => '<img src="https://openweathermap.org/images/flags/ua.png">',
      'icon' => '<img src="https://openweathermap.org/img/wn/01d.png">',
      'name' => 'Lviv',
      'country' => 'UA',
      'main' => 'Clear',
      'description' => 'clear sky',
      'temp' => '12.21',
      'feels_like' => '11.11',
      'humidity' => '62',
      'pressure' => '1024',
    ];

    $api_body_data = Json::decode($api_body_data_json);

    $this->assertEquals($test_data_response, WeatherWatcher::createWeatherData($api_body_data));
  }

}
