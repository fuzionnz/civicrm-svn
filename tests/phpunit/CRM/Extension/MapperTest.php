<?php

require_once 'CiviTest/CiviUnitTestCase.php';

class CRM_Extension_MapperTest extends CiviUnitTestCase {
  function setUp() {
    parent::setUp();
    list ($this->basedir, $this->container) = $this->_createContainer();
    $this->mapper = new CRM_Extension_Mapper($this->container);

    list ($this->basedir2, $this->containerWithSlash) = $this->_createContainer(NULL, NULL, '/');
    $this->mapperWithSlash = new CRM_Extension_Mapper($this->containerWithSlash);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testClassToKey() {
    $this->assertEquals("test.foo.bar", $this->mapper->classToKey('test_foo_bar'));
  }

  function testClassToPath() {
    $this->assertEquals("{$this->basedir}/weird/foobar/oddball.php", $this->mapper->classToPath('test_foo_bar'));
  }

  function testIsExtensionClass() {
    $this->assertTrue($this->mapper->isExtensionClass('test_foo_bar'));
    $this->assertFalse($this->mapper->isExtensionClass('test.foo.bar'));
    $this->assertFalse($this->mapper->isExtensionClass('CRM_Core_DAO'));
  }

  function testIsExtensionKey() {
    $this->assertFalse($this->mapper->isExtensionKey('test_foo_bar'));
    $this->assertTrue($this->mapper->isExtensionKey('test.foo.bar'));
    $this->assertFalse($this->mapper->isExtensionKey('CRM_Core_DAO'));
  }

  function testGetTemplateName() {
    $this->assertEquals("oddball.tpl", $this->mapper->getTemplateName('test_foo_bar'));
  }

  function testGetTemplatePath() {
    $this->assertEquals("{$this->basedir}/weird/foobar/templates", $this->mapper->getTemplatePath('test_foo_bar'));
  }

  function testKeyToClass() {
    $this->assertEquals("test_foo_bar", $this->mapper->keyToClass('test.foo.bar'));
  }

  function testKeyToPath() {
    $this->assertEquals("{$this->basedir}/weird/foobar/oddball.php", $this->mapper->classToPath('test.foo.bar'));
    $this->assertEquals("{$this->basedir2}/weird/foobar/oddball.php", $this->mapperWithSlash->classToPath('test.foo.bar'));
  }

  function testKeyToUrl() {
    $this->assertEquals("http://example/basedir/weird/foobar", $this->mapper->keyToUrl('test.foo.bar'));
    $this->assertEquals("http://example/basedir/weird/foobar", $this->mapperWithSlash->keyToUrl('test.foo.bar'));

    $config = CRM_Core_Config::singleton();
    $this->assertEquals(rtrim($config->resourceBase, '/'), $this->mapper->keyToUrl('civicrm'));
    $this->assertEquals(rtrim($config->resourceBase, '/'), $this->mapperWithSlash->keyToUrl('civicrm'));
  }

  function _createContainer(CRM_Utils_Cache_Interface $cache = NULL, $cacheKey = NULL, $appendPathGarbage = '') {
    /*
    $container = new CRM_Extension_Container_Static(array(
      'test.foo.bar' => array(
        'path' => '/path/to/test.foo.bar',
        'resUrl' => 'http://resources/test.foo.bar',
      ),
    ));
    */
    $basedir = rtrim($this->createTempDir('ext-'), '/');
    mkdir("$basedir/weird");
    mkdir("$basedir/weird/foobar");
    file_put_contents("$basedir/weird/foobar/info.xml", "<extension key='test.foo.bar' type='report'><file>oddball</file></extension>");
    // not needed for now // file_put_contents("$basedir/weird/bar/oddball.php", "<?php\n");
    $c = new CRM_Extension_Container_Basic($basedir . $appendPathGarbage, 'http://example/basedir' . $appendPathGarbage, $cache, $cacheKey);
    return array($basedir, $c);
  }
}
