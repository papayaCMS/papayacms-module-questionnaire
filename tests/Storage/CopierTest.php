<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Connector.php');
require_once(dirname(__FILE__).'/../../src/Storage/Copier.php');

class PapayaQuestionnaireStorageCopierTest extends PapayaTestCase {

  public function setUp() {
    $this->c = new PapayaQuestionnaireStorageCopier();
  }

  /**
  * @covers PapayaQuestionnaireStorageCopier::setConnector
  */
  public function testSetConnector() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector');
    $this->c->setConnector($mockConnector);
    $this->assertSame($mockConnector, $this->readAttribute($this->c, '_connector'));
  }

  /**
  * @covers PapayaQuestionnaireStorageCopier::getConnector
  */
  public function testGetConnector() {
    $this->assertTrue($this->c->getConnector() instanceof PapayaQuestionnaireConnector);
  }

  public function testCopyPool() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array(
      'getPools', 'createPool', 'getGroups', 'createGroup', 'getQuestions', 'createQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('getPools')
      ->will($this->returnValue(array(3 => array('question_pool_id' => 3, 'question_pool_name' => 'My Pool'))));
    $mockConnector
      ->expects($this->once())
      ->method('createPool')
      ->with($this->equalTo(array('question_pool_id' => 3, 'question_pool_name' => 'Copy of My Pool')))
      ->will($this->returnValue(7));
    $mockConnector
      ->expects($this->once())
      ->method('getGroups')
      ->will($this->returnValue(array(1 => array('question_group_id' => 1, 'question_group_name' => 'My Group', 'question_group_identifier' => 'mg'))));
    $mockConnector
      ->expects($this->once())
      ->method('createGroup')
      ->with($this->equalTo(array('question_group_id' => 1, 'question_group_name' => 'My Group', 'question_group_identifier' => 'mg', 'question_pool_id' => 7)))
      ->will($this->returnValue(9));
    $mockConnector
      ->expects($this->once())
      ->method('getQuestions')
      ->will($this->returnValue(array(6 => array('question_id' => 6, 'question_position' => 3, 'question_group_id' => 9, 'question_identifier' => 'q6',
             'question_type' => 'testtype', 'question_text' => 'Question?', 'question_answer_data' => '<data></data>'))));
    $mockConnector
      ->expects($this->once())
      ->method('createQuestion')
      ->with($this->equalTo(array('question_id' => 6, 'question_position' => 3, 'question_group_id' => 9, 'question_identifier' => 'q6',
             'question_type' => 'testtype', 'question_text' => 'Question?', 'question_answer_data' => '<data></data>')));
    $this->c->setConnector($mockConnector);
    $this->c->copyPool(3);
  }

}
