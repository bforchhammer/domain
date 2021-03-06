<?php

/**
 * @file
 * Definition of Drupal\domain\Tests\DomainForms
 */

namespace Drupal\domain\Tests;
use Drupal\domain\DomainInterface;

/**
 * Tests the domain record interface.
 */
class DomainForms extends DomainTestBase {

  public static function getInfo() {
    return array(
      'name' => 'Domain form interface',
      'description' => 'Tests the domain record user interface.',
      'group' => 'Domain',
    );
  }

  /**
   * Create, edit and delete a domain via the user interface.
   */
  function testDomainInterface() {
    $this->admin_user = $this->drupalCreateUser(array('administer domains', 'create domains'));
    $this->drupalLogin($this->admin_user);

    // No domains should exist.
    $this->domainTableIsEmpty();

    // Visit the main domain administration page.
    $this->drupalGet('admin/structure/domain');

    // Check for the add message.
    $this->assertText('There is no Domain record yet.', 'Text for no domains found.');
    // Visit the add domain administration page.
    $this->drupalGet('admin/structure/domain/add');

    // Make a POST request on admin/structure/domain/add.
    $edit = $this->domainPostValues();
    $this->drupalPostForm('admin/structure/domain/add', $edit, 'Save');

    // Did it save correctly?
    $default_id = domain_default_id();
    $this->assertTrue(!empty($default_id), 'Domain record saved via form.');

    // Does it load correctly?
    $new_domain = domain_load($default_id);
    $this->assertTrue($new_domain->id() == $edit['id'], 'Domain loaded properly.');

    // Has a UUID been set?
    $this->assertTrue(!empty($new_domain->uuid), 'Entity UUID set properly.');

    // Visit the edit domain administration page.
    $postUrl = 'admin/structure/domain/edit/' . $new_domain->id();
    $this->drupalGet($postUrl);

    // Update the record.
    $edit['name'] = 'Foo';
    $this->drupalPostForm($postUrl, $edit, t('Save'));

    // Check that the update succeeded.
    $domain = domain_load($default_id, TRUE);
    $this->assertTrue($domain->name == 'Foo', 'Domain record updated via form.');

    // Delete the record.
    $this->drupalPostForm($postUrl, $edit, t('Delete'));
    $domain = domain_load($default_id, TRUE);
    $this->assertTrue(empty($domain), 'Domain record deleted.');


    // No domains should exist.
    $this->domainTableIsEmpty();
  }

}
