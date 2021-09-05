<?php

namespace Drupal\Tests\current_weather\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Create a node and test edit permissions.
 *
 * @group node
 */
class CurrentWeatherPreprocessTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['current_weather'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests an anonymous and unpermissioned user attempting to edit the node.
   */
  public function testPageView() {
    // Verify that anonimus user can access simple content.
    $this->pageExampleVerifyNoAccess('weather', '403');
    $this->pageExampleVerifyNoAccess('weather/Ostroh', '403');
    $this->pageExampleVerifyNoAccess('weather/Ostroh/UA', '403');

    $this->drupalLogin($this->drupalCreateUser([
          'administer current_weather configuration',
        ]));

    // Verify that user can access simple content.
    $this->pageExampleVerifyNoAccess('weather', '200');
    $this->pageExampleVerifyNoAccess('weather/Ostroh', '200');
    $this->pageExampleVerifyNoAccess('weather/Ostroh/UA', '200');

    $this->assertSession()->pageTextContains('Weather for');
  }

  /**
   * Verify that current user has no access to page.
   *
   * @param string $url
   *   URL to verify.
   */
  public function pageExampleVerifyNoAccess($url, $code = 200) {
    // Test that page returns 403 Access Denied.
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals($code);
  }

}
