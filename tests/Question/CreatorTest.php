<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Question/Creator.php');

class PapayaQuestionnaireQuestionCreatorTest extends PapayaQuestionnaireTestCase {

  public function setUp() {
    $this->q = new PapayaQuestionnaireQuestionCreator;
  }

  /**
  * @covers PapayaQuestionnaireQuestionCreator::getConfiguration
  */
  public function testGetConfiguration() {
    $creator = new PapayaQuestionnaireQuestionCreator();
    $mockConfiguration = $this->getMockConfigurationObject();
    $mockApplication = $this->getMockApplicationObject(array('options' => $mockConfiguration));
    $creator->papaya($mockApplication);
    $this->assertEquals($mockConfiguration, $creator->getConfiguration());
  }

  /**
  * @covers PapayaQuestionnaireQuestionCreator::registerQuestionType
  */
  public function testRegisterQuestionType() {
    $existingTypes = $this->readAttribute($this->q, '_questionTypes');
    $this->q->registerQuestionType('test_identifier', 'Test Questiontype', 'test_file.php', 'testClass');
    $newType = array(
      'test_identifier' => array(
        'name' => 'Test Questiontype',
        'file' => 'test_file.php',
        'class' => 'testClass',
        )
    );
    $this->assertAttributeEquals(array_merge($existingTypes, $newType), '_questionTypes', $this->q);
  }


  /**
  * @covers PapayaQuestionnaireQuestionCreator::getQuestionObject
  */
  public function testGetQuestionObject() {
    $this->q->setConfiguration($this->getMockConfigurationObject());
    $result = $this->q->getQuestionObject('single_choice_5');
    $this->assertTrue($result instanceof PapayaQuestionnaireQuestionSingleChoice5);
  }

  /**
  * @covers PapayaQuestionnaireQuestionCreator::getQuestionObject
  */
  public function testGetQuestionObjectAbsolutePath() {
    $this->q->setConfiguration($this->getMockConfigurationObject());
    $this->q->registerQuestionType(
      'test_absolute_path',
      'Test absolute path',
      dirname(__FILE__).'/../../src/Question/SingleChoice5.php',
      'PapayaQuestionnaireQuestionSingleChoice5'
    );
    $result = $this->q->getQuestionObject('test_absolute_path');
    $this->assertTrue($result instanceof PapayaQuestionnaireQuestionSingleChoice5);
  }

  /**
  * @covers PapayaQuestionnaireQuestionCreator::getQuestionObject
  */
  public function testGetQuestionObjectInvalidFile() {
    $this->q->setConfiguration($this->getMockConfigurationObject());
    $this->q->registerQuestionType(
      'test_no_file',
      'Test no file',
      dirname(__FILE__).'_nonexisting_file_'.uniqid(),
      'PapayaQuestionnaireQuestionSingleChoice5'
    );
    $result = $this->q->getQuestionObject('test_no_file');
    $this->assertFalse($result instanceof PapayaQuestionnaireQuestionSingleChoice5);
  }

  /**
  * @covers PapayaQuestionnaireQuestionCreator::getQuestionTypes
  */
  public function testGetQuestionTypes() {
    $this->q->setConfiguration($this->getMockConfigurationObject());
    $this->assertEquals(count($this->q->getQuestionTypes()), count($this->readAttribute($this->q, '_questionTypes')));
  }
}
