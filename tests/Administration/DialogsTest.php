<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Administration/Dialogs.php');
require_once(dirname(__FILE__).'/../../src/Question.php');
require_once(dirname(__FILE__).'/../../src/Question/Abstract.php');
require_once(dirname(__FILE__).'/../../src/Question/Creator.php');
require_once(dirname(__FILE__).'/../../src/Dialog/Builder.php');

class PapayaQuestionnaireAdministrationDialogsTest extends PapayaTestCase {

  public function setUp() {
    $this->d = new PapayaQuestionnaireAdministrationDialogs;
  }

  public function getQuestionMock() {
    return $this->getMock(
      'PapayaQuestionnaireQuestionProxy',
      array(
        'setId' ,
        'getId' ,
        'setGroupId',
        'getGroupId',
        'setText',
        'getText',
        'setType',
        'getType',
        'getConfigurationDialog',
        'getDialogData',
        'getQuestionConfiguration',
        'getAnswers',
        'getQuestionXML',
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::setParameters
  */
  public function testSetParameters() {
    $params = array('ping' => 'pong');
    $this->d->setParameters($params);
    $this->assertEquals($params, $this->readAttribute($this->d, 'params'));
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::setParamName
  */
  public function testSetParamName() {
    $this->d->setParamName('ping');
    $this->assertEquals('ping', $this->readAttribute($this->d, 'paramName'));
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::setQuestionTypes
  */
  public function testSetQuestionTypes() {
    $params = array('ping' => 'pong');
    $this->assertTrue($this->d->setQuestionTypes($params));
    $this->assertEquals($params, $this->readAttribute($this->d, '_questionTypes'));
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::setConfiguration
  */
  public function testSetConfiguration() {
    $this->d->setConfiguration($options = $this->getMockConfigurationObject());
    $this->assertAttributeSame($options, '_configuration', $this->d);
  }
  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::setQuestionCreator
  */
  public function testSetQuestionCreator() {
    $mockCreator = $this->getMock('PapayaQuestionnaireQuestionCreator');
    $this->assertTrue($this->d->setQuestionCreator($mockCreator));
    $this->assertSame($mockCreator, $this->readAttribute($this->d, '_questionCreator'));
  }

  public function testGetQuestionCreator() {
    $this->d->setConfiguration($options = $this->getMockConfigurationObject());
    $this->assertTrue($this->d->getQuestionCreator() instanceof PapayaQuestionnaireQuestionCreator);
  }


  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::setDialogBuilder
  */
  public function testSetDialogBuilder() {
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder');
    $this->d->setDialogBuilder($mockBuilder);
    $this->assertSame($mockBuilder, $this->readAttribute($this->d, '_dialogBuilder'));
  }

  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::getDialogBuilder
  */
  public function testGetDialogBuilder() {
    $this->assertTrue($this->d->getDialogBuilder() instanceof PapayaQuestionnaireDialogBuilder);
  }

  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::getPoolDialog
  */
  public function testGetPoolDialog() {
    $params = array(
      'cmd' => 'test',
    );
    $paramName = 'test';
    $fields = array(
      'question_pool_name' => array('Pool name', 'isNoHTML', TRUE, 'input', 200, '', ''),
      'question_pool_identifier' => array('Pool identifier', 'isNoHTML', FALSE, 'input', 8, '', ''),
    );
    $hidden = array(
      'cmd' => $params['cmd'],
      'submit' => 1,
    );
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogBasic');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createDialog'));
    $mockBuilder
      ->expects($this->once())
      ->method('createDialog')
      ->with($this->equalTo($this->d), $paramName, $fields, array(), $hidden)
      ->will($this->returnValue($mockDialog));

    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getPoolDialog();
  }

  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::getDeletePoolDialog
  */
  public function testGetDeletePoolDialog() {
    $params = array(
      'cmd' => 'test',
      'question_pool_id' => 1,
    );
    $paramName = 'test';
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_pool_id' => 1,
      'submit' => 1,
    );
    $msg = 'Do you really want to delete pool #1 including groups and questions?';
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogMessage');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createMsgDialog'));
    $mockBuilder
      ->expects($this->once())
      ->method('createMsgDialog')
      ->with($this->equalTo($this->d), $paramName, $hidden, $msg, 'warning')
      ->will($this->returnValue($mockDialog));

    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getDeletePoolDialog();
  }

  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::getGroupDialog
  */
  public function testGetGroupDialog() {
    $params = array(
      'cmd' => 'test',
      'question_pool_id' => 1,
      'question_group_id' => 2,
    );
    $paramName = 'test';
    $fields = array(
      'question_group_name' => array('Group name', 'isNoHTML', TRUE, 'input', 250, '', ''),
      'question_group_subtitle' => array('Group subtitle', 'isNoHTML', TRUE, 'input', 250, '', ''),
      'question_group_identifier' => array('Group identifier', 'isNoHTML', TRUE, 'input', 8, '', ''),
      'question_group_text' =>
        array('Group text', 'isSomeText', FALSE, 'richtext', 10, '', ''),
      'question_group_min_answers' => array('Minimum answers', 'isNum', FALSE, 'input', 10, '', 0),
    );
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_pool_id' => 1,
      'submit' => 1,
      'question_group_id' => 2,
    );
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogBasic');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createDialog'));
    $mockBuilder
      ->expects($this->once())
      ->method('createDialog')
      ->with($this->equalTo($this->d), $this->equalTo($paramName), $this->equalTo($fields), $this->equalTo(array('question_group_id' => 2)), $this->equalTo($hidden))
      ->will($this->returnValue($mockDialog));

    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getGroupDialog(array('question_group_id' => 2));
  }

  /**
  * @covers  PapayaQuestionnaireAdministrationDialogs::getDeleteGroupDialog
  */
  public function testGetDeleteGroupDialog() {
    $params = array(
      'cmd' => 'test',
      'question_group_id' => 1,
    );
    $paramName = 'test';
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_group_id' => 1,
      'submit' => 1,
    );
    $msg = 'Do you really want to delete group #1 and its questions?';
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogMessage');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createMsgDialog'));
    $mockBuilder
      ->expects($this->once())
      ->method('createMsgDialog')
      ->with($this->equalTo($this->d), $paramName, $hidden, $msg, 'warning')
      ->will($this->returnValue($mockDialog));

    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getDeleteGroupDialog();
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::getQuestionDialog
  */
  public function testGetQuestionDialog() {
    $questionTypes = array(
      'physican_appraisal' => 'Physician Appraisal',
    );
    $params = array(
      'cmd' => 'test',
      'question_group_id' => 1,
      'question_id' => 3,
    );
    $paramName = 'test';
    $fields = array(
      'question_text' => array('Question', 'isNoHTML', TRUE, 'input', 200, '', ''),
      'question_identifier' => array('Question identifier', 'isNoHTML', TRUE, 'input', 8, '', ''),
      'question_type' => array('Question type', 'isNoHTML', TRUE, 'combo', $questionTypes, '', '')
    );
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_group_id' => 1,
      'step' => 1,
      'question_id' => 1,
    );
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogBasic');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createDialog'));
    $data = array();
    $mockBuilder
      ->expects($this->once())
      ->method('createDialog')
      ->with($this->equalTo($this->d), $this->equalTo($paramName), $this->equalTo($fields), $this->equalTo($data), $this->equalTo($hidden))
      ->will($this->returnValue($mockDialog));

    $mockQuestion = $this->getQuestionMock('getId');
    $mockQuestion
      ->expects($this->atLeastOnce())
      ->method('getId')
      ->will($this->returnValue(1));

    $this->d->setQuestionTypes($questionTypes);
    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getQuestionDialog($mockQuestion);
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::getQuestionDialog
  */
  public function testGetQuestionDialogStep2() {
    $questionTypes = array(
      'physician_appraisal' => 'Physician Appraisal',
    );
    $params = array(
      'cmd' => 'test',
      'question_group_id' => 1,
      'step' => 1,
      'question_type' => 'physician_appraisal',
      'question_text' => 'What was the question again?',
      'question_identifier' => 'q1',
      'question_answer_data' => array('<data></data>'),
      'question_id' => 3,
    );
    $paramName = 'test';
    $fields = array(
    );
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_group_id' => 1,
      'submit' => 1,
      'question_id' => 3,
    );

    $mockQuestion = $this->getQuestionMock(array('getConfigurationDialog'));
    $mockQuestion
      ->expects($this->once())
      ->method('getConfigurationDialog');

    $mockQuestionCreator = $this->getMock('PapayaQuestionnaireQuestionCreator');

    $this->d->setQuestionTypes($questionTypes);
    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setQuestionCreator($mockQuestionCreator);
    $this->d->getQuestionDialog($mockQuestion);
  }

  /**
  * @covers PapayaQuestionnaireAdministrationDialogs::getDeleteQuestionDialog
  */
  public function testGetDeleteQuestionDialog() {
    $params = array(
      'cmd' => 'test',
      'question_id' => 1,
    );
    $paramName = 'test';
    $hidden = array(
      'cmd' => $params['cmd'],
      'question_id' => 1,
      'submit' => 1,
    );
    $msg = 'Do you really want to delete question #1?';
    $mockDialog = $this->getMock('PapayaQuestionnaireDialogMessage');
    $mockBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createMsgDialog'));
    $mockBuilder
      ->expects($this->once())
      ->method('createMsgDialog')
      ->with($this->equalTo($this->d), $paramName, $hidden, $msg, 'warning')
      ->will($this->returnValue($mockDialog));

    $this->d->setParamName($paramName);
    $this->d->setParameters($params);
    $this->d->setDialogBuilder($mockBuilder);
    $this->d->getDeleteQuestionDialog();
  }
}

class PapayaQuestionnaireQuestionProxy
  extends PapayaQuestionnaireQuestionAbstract
  implements PapayaQuestionnaireQuestion {

  public function setId($id) {}

  public function getId() {}

  public function setGroupId($groupId) {}

  public function getGroupId() {}

  public function setText($text) {}

  public function getText() {}

  public function setType($type) {}

  public function getType() {}

  public function getConfigurationDialog() {}

  public function getDialogData() {}

  public function getQuestionConfiguration() {}


  public function getAnswers() {}

  public function getQuestionXML($questionId, $selectedAnswer = FALSE) {}

}
