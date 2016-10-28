<?php

/**
 * @file
 * Base phpunit tests for HarvestSource class.
 */

include_once __DIR__ . '/includes/HarvestSourceTestStub.php';

/**
 *
 */
class HarvestSourceTest extends \PHPUnit_Framework_TestCase {

  // dkan_harvest_test status.
  public static $dkanHarvestTestBeforClassStatus = TRUE;

  /**
   * {@inheritdoc}
   */
  public static function setUpBeforeClass() {
    // Make sure the test module exporting the test source type.
    if (!module_exists('dkan_harvest_test')) {
      self::$dkanHarvestTestBeforClassStatus = FALSE;
      module_enable(array('dkan_harvest_test'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setup() {
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage machine name is required!
   */
  public function testHarvestSourceConstructMachineNameNULLException() {
    $source = new HarvestSource(NULL);
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage machine name is required!
   */
  public function testHarvestSourceConstructMachineNameEmptyException() {
    $source = new HarvestSource('');
  }

  /**
   * Test a valid HarvestSource instantiation.
   */
  public function testHarvestSourceConstruct() {
    // Create and save the harvest source node.
    $node = new stdClass();
    $node->title = 'testHarvestSourceConstructTitle';
    $node->type = "harvest_source";
    node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
    $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
    $node->uid = 1;
    $node->status = 1; //(1 or 0): published or not
    $node->promote = 0; //(1 or 0): promoted to front page
    $node->comment = 0; // 0 = comments disabled, 1 = read only, 2 = read/write

    $node->field_dkan_harvest_machine_name[$node->language][]['machine'] = 'test_harvest_source_construct';

    $node->field_dkan_harvest_source_uri[$node->language][0]['value'] = 'https://data.mo.gov/data.json';
    $node->field_dkan_harvest_source_uri[$node->language][0]['safe_value'] = 'https://data.mo.gov/data.json';

    $node->field_dkan_harveset_type[$node->language][]['value'] = 'harvest_test_type';

    $node = node_submit($node); // Prepare node for saving
    node_save($node);

    $this->testHarvestSourceConstructNID = $node->nid;

    // Get the HarvestSource object.
    $source = new HarvestSource('test_harvest_source_construct');

    $this->assertNotNull($source);
    $this->assertEquals($source->label, $node->title);
    $this->assertEquals($source->type,
      HarvestSourceType::getSourceType($node->field_dkan_harveset_type[$node->language][0]['value']));
    $this->assertEquals($source->uri, $node->field_dkan_harvest_source_uri[$node->language][0]['safe_value']);

  }

  /**
   * covers HarvestSource::isRemote
   */
  public function  testIsRemote() {
    $source_remote = $this->getRemoteSource();
    $this->assertTrue($source_remote->isRemote());

    $source_local = $this->getLocalSource();
    $this->assertFalse($source_local->isRemote());
  }

  /**
   * covers HarvestSource::getCacheDir
   */
  public function testGetCacheDir() {
    $source_remote = $this->getRemoteSource();
    $source_remote_cachedir_path = DKAN_HARVEST_CACHE_DIR .
      '/' .
      $source_remote->machine_name;
    // Make sure that we delete the cache directory.
    file_unmanaged_delete_recursive($source_remote_cachedir_path);
    $rmdir = drupal_rmdir($source_remote_cachedir_path);

    $cacheDir = $source_remote->getCacheDir();
    $this->assertFALSE($cacheDir);

    $cacheDir = $source_remote->getCacheDir(TRUE);
    $this->assertEquals($cacheDir, $source_remote_cachedir_path);
  }

  /**
   *
   */
  public function testGetHarvestSourceFromNode() {
    // Stop here and mark this test as incomplete.
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
  }

  /**
   * {@inheritdoc}
   */
  public static function tearDownAfterClass() {
    // Assuming the test module enabled by now. Restore original status of the
    // modules.
    if (!self::$dkanHarvestTestBeforClassStatus) {
      module_disable(array('dkan_harvest_test'));
    }

    // Clean up after the testHarvestSourceConstruct test
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'harvest_source')
      ->fieldCondition('field_dkan_harvest_machine_name', 'machine', 'test_harvest_source_construct');
    $result = $query->execute();
    if ($result && isset($result['node'])) {
      node_delete_multiple(array_keys($result['node']));
    }
  }

  /**
   * Return Test HarvestSource object.
   */
  private function getRemoteSource() {
    return new HarvestSourceTestStub(
      'harvest_test_source_remote', 'https://data.mo.gov/data.json');
  }

  /**
   * Return Test HarvestSource object.
   */
  private function getLocalSource() {
    return new HarvestSourceTestStub(
      'harvest_test_source_local_file', __DIR__ . '/data/harvest_test_source_local_file/data.json'
    );
  }
}
