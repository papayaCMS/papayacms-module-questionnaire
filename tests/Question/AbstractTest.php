<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Question/Abstract.php');
require_once(dirname(__FILE__).'/../../src/Question/SingleChoice5.php');
require_once(dirname(__FILE__).'/../../src/Connector.php');
require_once(dirname(__FILE__).'/../../src/Dialog/Builder.php');

class PapayaQuestionnaireQuestionAbstractTest extends PapayaTestCase {

  public function setUp() {
    $this->q = new PapayaQuestionnaireQuestionAbstract;
    $this->q->setParamName('qpa');
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setConnector
  */
  public function testSetConnector() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector');
    $this->assertTrue($this->q->setConnector($mockConnector));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getConnector
  */
  public function testGetConnector() {
    $this->assertTrue($this->q->getConnector() instanceof PapayaQuestionnaireConnector);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setDialogBuilder
  */
  public function testSetDialogBuilder() {
    $mockDialogBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder');
    $this->q->setDialogBuilder($mockDialogBuilder);
    $this->assertAttributeSame($mockDialogBuilder, '_dialogBuilder', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getDialogBuilder
  */
  public function testGetDialogBuilder() {
    $this->assertInstanceOf('PapayaQuestionnaireDialogBuilder', $this->q->getDialogBuilder());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getConfigurationDialog
  */
  public function testGetConfigurationDialog() {
    $data = array();
    $mockS = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('getDialogData'));
    $mockS
      ->expects($this->once())
      ->method('getDialogData')
      ->will($this->returnValue($data));
    $mockDialog = $this->getMock('base_dialog', array(), array(), 'Mock_base_dialog_'.md5(uniqid()), FALSE, FALSE);
    $mockDialogBuilder = $this->getMock('PapayaQuestionnaireDialogBuilder', array('createDialog'));
    $mockDialogBuilder
      ->expects($this->once())
      ->method('createDialog')
      ->with(
        $this->equalTo($mockS),
        $this->equalTo($this->readAttribute($mockS, '_paramName')),
        $this->equalTo($this->readAttribute($mockS, '_configurationFields')),
        $this->equalTo($data),
        $this->equalTo($this->readAttribute($mockS, '_hiddenParameters'))
      )
      ->will($this->returnValue($mockDialog));
    $mockS->setDialogBuilder($mockDialogBuilder);
    $this->assertSame($mockDialog, $mockS->getConfigurationDialog());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::loadFromData
  * @dataProvider provideLoadFromData
  */
  public function testLoadFromData($data, $method, $value) {
    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestionAbstract', array($method));
    $mockQuestion
      ->expects($this->once())
      ->method($method)
      ->with($this->equalTo($value));
    $mockQuestion->loadFromData($data);
  }

  public function provideLoadFromData() {
    return array(
      'id' => array(
        array('question_id' => 3),
        'setId',
        3
      ),
      'position' => array(
        array('question_position' => 5),
        'setPosition',
        5
      ),
      'group id' => array(
        array('question_group_id' => 4),
        'setGroupId',
        4
      ),
      'identifier' => array(
        array('question_identifier' => 'q1'),
        'setIdentifier',
        'q1'
      ),
      'type' => array(
        array('question_type' => 'my_question_type'),
        'setType',
        'my_question_type'
      ),
      'text' => array(
        array('question_text' => 'This is the question text.'),
        'setText',
        'This is the question text.'
      ),
      'answer data' => array(
        array('question_answer_data' => '<xml></xml>'),
        'setConfigurationData',
        '<xml></xml>'
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::loadFromData
  */
  public function testLoadFromDataConfigurations() {
    $mockQ = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('setQuestionConfigurationValue'));
    $mockQ
      ->expects($this->once())
      ->method('setQuestionConfigurationValue')
      ->with($this->equalTo('test'), $this->equalTo('tesa'));
    $mockQ->setConfigurationFields(array('test' => array()));
    $mockQ->loadFromData(array('test' => 'tesa'));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::loadFromData
  */
  public function testLoadFromDataAnswerText() {
    $mockQ = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('setAnswerText'));
    $mockQ
      ->expects($this->once())
      ->method('setAnswerText')
      ->with($this->equalTo(1), $this->equalTo('tesa'));
    $mockQ->setAnswerFields(array('answer_1' => 'answer_value_1'));
    $mockQ->loadFromData(array('answer_1' => 'tesa'));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::loadFromData
  */
  public function testLoadFromDataAnswerValue() {
    $mockQ = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('setAnswerValue'));
    $mockQ
      ->expects($this->once())
      ->method('setAnswerValue')
      ->with($this->equalTo(1), $this->equalTo('tesa'));
    $mockQ->setAnswerFields(array('answer_1' => 'answer_value_1'));
    $mockQ->loadFromData(array('answer_value_1' => 'tesa'));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setConfigurationData
  */
  public function testSetConfigurationDataArray() {
    $data = array('some' => 'data');
    $this->q->setConfigurationData($data);
    $this->assertAttributeEquals($data, '_questionConfig', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getQuestionConfiguration
  */
  public function testGetQuestionConfiguration() {
    $data = array('some' => 'data');
    $this->q->setConfigurationData($data);
    $this->assertEquals($data, $this->q->getQuestionConfiguration());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setConfigurationData
  */
  public function testSetConfigurationDataXML() {
    $expected = array('some' => 'data');
    $data = '<data><data-element name="some">data</data-element></data>';
    $this->q->setConfigurationFields(array('some' => 'tesa'));
    $this->q->setConfigurationData($data);
    $this->assertAttributeEquals($expected, '_questionConfig', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getConfigurationDataXML
  */
  public function testGetAndSetConfigurationData() {
    $expected = '<data version="2"><data-element name="some">data</data-element></data>';
    $data = array('some' => 'data');
    $this->q->setConfigurationData($data);
    $this->assertEquals($expected, $this->q->getConfigurationDataXML());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getConfigurationOption
  */
  public function testGetConfigurationOption() {
    $data = array('some' => 'data');
    $this->q->setConfigurationFields(array('some' => 'tesa'));
    $this->q->setConfigurationData($data);
    $this->assertSame('data', $this->q->getConfigurationOption('some'));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getAnswerText
  * @covers PapayaQuestionnaireQuestionAbstract::setAnswerText
  */
  public function testGetSetAnswerText() {
    $this->q->setAnswerText(1, 'testtext');
    $this->assertEquals('testtext', $this->q->getAnswerText(1));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getAnswerValue
  * @covers PapayaQuestionnaireQuestionAbstract::setAnswerValue
  */
  public function testGetSetAnswerValue() {
    $this->q->setAnswerValue(1, 'value');
    $this->assertEquals('value', $this->q->getAnswerValue(1));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getAnswerOptionId
  * @covers PapayaQuestionnaireQuestionAbstract::setAnswerOptionId
  */
  public function testGetSetAnswerOptionId() {
    $this->q->setAnswerOptionId(1, 123);
    $this->assertEquals(123, $this->q->getAnswerOptionId(1));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setAnswers
  * @covers PapayaQuestionnaireQuestionAbstract::getAnswers
  * @covers PapayaQuestionnaireQuestionAbstract::unsetAnswers
  */
  public function testSetGetUnsetAnswers() {
    $answers = array(
      21 => array('text' => 'answer text', 'value' => 123),
      22 => array('text' => 'answer text 2', 'value' => 234),
    );

    $expected = array(
      1 => array(
        'text' => 'answer text',
        'value' => 123,
        'answer_id' => 21,
      ),
      2 => array(
        'text' => 'answer text 2',
        'value' => 234,
        'answer_id' => 22,
      ),
    );

    $this->q->setAnswers($answers);
    $this->assertAttributeEquals($answers, '_answers', $this->q);
    $this->assertEquals($expected, $this->q->getAnswers());
    $this->q->unsetAnswers($answers);
    $this->assertAttributeEquals(array(), '_answers', $this->q);
    $this->assertAttributeEquals(array(), '_answerTexts', $this->q);
    $this->assertAttributeEquals(array(), '_answerValues', $this->q);
    $this->assertAttributeEquals(array(), '_answerOptionIds', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setId
  * @covers PapayaQuestionnaireQuestionAbstract::getId
  */
  public function testSetGetId() {
    $this->q->setId(123);
    $this->assertAttributeEquals(123, '_id', $this->q);
    $this->assertEquals(123, $this->q->getId());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setGroupId
  * @covers PapayaQuestionnaireQuestionAbstract::getGroupId
  */
  public function testSetGetGroupId() {
    $this->q->setGroupId(12);
    $this->assertAttributeEquals(12, '_groupId', $this->q);
    $this->assertEquals(12, $this->q->getGroupId());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setText
  * @covers PapayaQuestionnaireQuestionAbstract::getText
  */
  public function testSetGetText() {
    $this->q->setText('a question text');
    $this->assertAttributeEquals('a question text', '_questionText', $this->q);
    $this->assertEquals('a question text', $this->q->getText());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setType
  * @covers PapayaQuestionnaireQuestionAbstract::getType
  */
  public function testSetGetType() {
    $this->q->setType('question_type');
    $this->assertAttributeEquals('question_type', '_questionType', $this->q);
    $this->assertEquals('question_type', $this->q->getType());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setPosition
  * @covers PapayaQuestionnaireQuestionAbstract::getPosition
  */
  public function testSetGetPosition() {
    $this->q->setPosition(3);
    $this->assertAttributeEquals(3, '_position', $this->q);
    $this->assertEquals(3, $this->q->getPosition());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setIdentifier
  * @covers PapayaQuestionnaireQuestionAbstract::getIdentifier
  */
  public function testSetGetIdentifier() {
    $this->q->setIdentifier('q1');
    $this->assertAttributeEquals('q1', '_identifier', $this->q);
    $this->assertEquals('q1', $this->q->getIdentifier());
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setConfiguration
  */
  public function testSetConfiguration() {
    $mockConfig = $this->getMockConfigurationObject();
    $this->q->setConfiguration($mockConfig);
    $this->assertSame($mockConfig, $this->readAttribute($this->q, '_configuration'));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setQuestionConfigurationValue
  */
  public function testSetQuestionConfigurationValue() {
    $this->q->setQuestionConfigurationValue('test', 'tesa');
    $this->assertAttributeEquals(array('test' => 'tesa'), '_questionConfig', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::addHiddenParameter
  */
  public function testAddHiddenParameter() {
    $this->q->addHiddenParameter('ping', 'pong');
    $params = $this->readAttribute($this->q, '_hiddenParameters');
    $this->assertTrue(isset($params['ping']) && $params['ping'] == 'pong');
  }


  /**
  * @covers PapayaQuestionnaireQuestionAbstract::replaceTemplateValues
  */
  public function testReplaceTemplateValues() {
    $content = 'The {%EXAMPLE%} template';
    $replacements = array('EXAMPLE' => 'test');
    $expected = 'The test template';
    $mockTemplate = $this->getMock('base_simpletemplate', array('parse'));
    $mockTemplate
      ->expects($this->once())
      ->method('parse')
      ->with($this->equalTo($content), $this->equalTo($replacements))
      ->will($this->returnValue($expected));
    $this->q->setTemplateObject($mockTemplate);
    $this->assertEquals($expected, $this->q->replaceTemplateValues($content, $replacements));
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::_getTemplateObject
  */
  public function testGetTemplateObject() {
    $proxyQ = $this->getProxy('PapayaQuestionnaireQuestionAbstract', array('_getTemplateObject'));
    $this->assertTrue($proxyQ->_getTemplateObject() instanceof base_simpletemplate);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setTemplateObject
  */
  public function testSetTemplateObject() {
    $mockTemplate = $this->getMock('base_simpletemplate');
    $this->q->setTemplateObject($mockTemplate);
    $this->assertAttributeEquals($mockTemplate, '_template', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setConfigurationFields
  */
  public function testSetConfigurationFields() {
    $expected = array('test' => 'tesa');
    $this->q->setConfigurationFields($expected);
    $this->assertAttributeEquals($expected, '_configurationFields', $this->q);
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::setAnswerFields
  */
  public function testSetAnswerFields() {
    $expected = array('test' => 'tesa');
    $this->q->setAnswerFields($expected);
    $this->assertAttributeEquals($expected, '_answerFields', $this->q);
  }
}


class QuestionnaireQuestionAbstractProxy extends PapayaQuestionnaireQuestionAbstract {
  public $_configurationFields;
}
