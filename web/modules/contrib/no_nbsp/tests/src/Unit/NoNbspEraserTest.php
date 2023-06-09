<?php

namespace Drupal\Tests\no_nbsp\Unit;

use Drupal\KernelTests\KernelTestBase;

/**
 * Run unit tests on the _no_nbsp_eraser function.
 *
 * @group no_nbsp
 */
class NoNbspEraserTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['no_nbsp'];

  /**
   * Test the function _no_nbsp_eraser.
   */
  public function testFunctionNoNbspEraser() {
    $this->assertEquals(_no_nbsp_eraser('l&nbsp;o&nbsp;l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l&nbsp;&nbsp;o&nbsp;&nbsp;l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l&nbsp; o&nbsp; l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l &nbsp; o &nbsp; l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l &nbsp;o &nbsp;l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l  o  l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l o l'), 'l o l');
    $this->assertEquals(_no_nbsp_eraser('l&nbspol'), 'l&nbspol');
    $this->assertEquals(_no_nbsp_eraser(' '), ' ');
    $this->assertEquals(_no_nbsp_eraser('&nbsp;'), ' ');
    $this->assertEquals(_no_nbsp_eraser('&nbsp;&nbsp;&nbsp;'), ' ');
    $this->assertEquals(_no_nbsp_eraser('&NBSP;'), ' ');
    $this->assertEquals(_no_nbsp_eraser('<p>&nbsp;</p>', TRUE), '<p>&nbsp;</p>');
  }

}
