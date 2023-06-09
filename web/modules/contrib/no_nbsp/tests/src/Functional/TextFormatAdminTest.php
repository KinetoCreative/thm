<?php

namespace Drupal\Tests\no_nbsp\Functional;

/**
 * Functional tests.
 *
 * Add different text formats via the admin interface and create some nodes
 * with or without the no non-breaking space filter.
 *
 * @group no_nbsp
 */
class TextFormatAdminTest extends NoNbspWebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['no_nbsp'];

  /**
   * {@inheritdoc}
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->drupalCreateContentType(['name' => 'page', 'type' => 'page']);
    $this->user = $this->drupalCreateUser([
      'administer filters',
      'bypass node access',
      'administer content types',
    ]);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests the format administration functionality.
   */
  public function testFormatAdmin() {
    // Check format add page.
    $this->drupalGet('admin/config/content/formats');
    $this->clickLink('Add text format');
    $this->assertSession()->pageTextContains(t('No Non-breaking Space Filter'));
    $this->assertSession()->pageTextContains(t('Delete all non-breaking space HTML entities.'));
    $this->assertSession()->pageTextContains(t('Preserve placeholders.'));
    $this->assertSession()->pageTextContains(t('A placeholder non-breaking space is surrounded by a HTML tag'));

    // Add new format.
    $format_id = 'no_nbsp_format';
    $name = 'No nbsp filter format';
    $edit = [
      'format' => $format_id,
      'name' => $name,
      'roles[anonymous]' => 1,
      'roles[authenticated]' => 1,
      'filters[filter_no_nbsp][status]' => 1,
    ];
    $this->submitForm($edit, t('Save configuration'));

    // Text the filters tips.
    $this->drupalGet('filter/tips');
    $this->assertSession()->pageTextContains(t('All non-breaking space HTML entities are replaced by blank space characters.'));
    $this->assertSession()->pageTextContains(t('Multiple contiguous space characters are replaced by a single blank space character.'));

    // Show submitted format edit page.
    $this->drupalGet('admin/config/content/formats/manage/' . $format_id);

    $input = $this->xpath('//input[@id="edit-filters-filter-no-nbsp-status"]');
    $this->assertEquals($input[0]->getAttribute('checked'), 'checked');

    // Test the format object.
    filter_formats_reset();
    $formats = filter_formats();
    $this->assertSame($formats[$format_id]->get('name'), $name);

    // Check format overview page.
    $this->drupalGet('admin/config/content/formats');
    $this->assertSession()->pageTextContains($name);

    // Generate a page without the enabled text filter.
    $node = $this->createFormatAndNode('l&nbsp;&nbsp;&nbsp;o&nbsp;&nbsp;&nbsp;l', 0);
    $this->assertSession()->responseContains('l&nbsp;&nbsp;&nbsp;o&nbsp;&nbsp;&nbsp;l');
    $this->drupalGet('node/' . $node->id() . '/edit');
    // no_nbsp_format exists at this time.
    $this->assertSession()->pageTextContains(t('All non-breaking space HTML entities are replaced by blank space characters.'));
    $this->assertSession()->pageTextNotContains(t('Multiple contiguous space characters are replaced by a single blank space character.'));

    // Generate a page with the enabled text filter.
    $node = $this->createFormatAndNode('l&nbsp;&nbsp;&nbsp;o&nbsp;&nbsp;&nbsp;l', 1);
    $this->assertSession()->responseContains('l o l');
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertSession()->pageTextContains(t('All non-breaking space HTML entities are replaced by blank space characters.'));
    $this->assertSession()->pageTextNotContains(t('Multiple contiguous space characters are replaced by a single blank space character.'));
  }

}
