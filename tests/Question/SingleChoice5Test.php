<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Question/SingleChoice5.php');

class PapayaQuestionnaireQuestionSingleChoice5Test extends PapayaTestCase {

  public function setUp() {
    $this->q = new PapayaQuestionnaireQuestionSingleChoice5;
    $this->q->setParamName('qpa');
  }

  /**
  * @covers PapayaQuestionnaireQuestionAbstract::getDialogData
  */
  public function testGetDialogData() {
    $expected = array(
      'alignment' => 'horizontal',
      'type' => 'radio',
      'mandatory' => 0,
      'answer_1' => 'text 1',
      'answer_value_1' => 1,
      'answer_2' => 'text 2',
      'answer_value_2' => 2,
      'answer_3' => 'text 3',
      'answer_value_3' => 3,
      'answer_4' => 'text 4',
      'answer_value_4' => 4,
      'answer_5' => 'text 5',
      'answer_value_5' => 5
    );
    $mockQuestion = $this->getMock(
      'PapayaQuestionnaireQuestionSingleChoice5',
      array('getConfigurationOption', 'getAnswerText', 'getAnswerValue'));
    $mockQuestion
      ->expects($this->exactly(16))
      ->method('getConfigurationOption')
      ->will($this->onConsecutiveCalls(
        $this->returnValue(FALSE),
        $this->returnValue('horizontal'),
        $this->returnValue('radio'),
        $this->returnValue(0),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE),
        $this->returnValue(FALSE)
      ));
    $mockQuestion
      ->expects($this->exactly(5))
      ->method('getAnswerText')
      ->will($this->onConsecutiveCalls(
        $this->returnValue('text 1'),
        $this->returnValue('text 2'),
        $this->returnValue('text 3'),
        $this->returnValue('text 4'),
        $this->returnValue('text 5')
      ));
    $mockQuestion
      ->expects($this->exactly(5))
      ->method('getAnswerValue')
      ->will($this->onConsecutiveCalls(
        $this->returnValue('1'),
        $this->returnValue('2'),
        $this->returnValue('3'),
        $this->returnValue('4'),
        $this->returnValue('5')
      ));
    $this->assertEquals($expected, $mockQuestion->getDialogData());
  }

  /**
  * @covers PapayaQuestionnaireQuestionSingleChoice5::getQuestionXML
  */
  public function testGetQuestionXML() {
    $question = array(
      'question_id' => 1,
      'question_group_id' => 1,
      'question_identifier' => 'q4-1',
      'question_type' => 'SingleChoice5',
      'question_text' => 'Wie finden Sie diesen Test?',
      'question_answer_data' => '<data><data-element name="alignment"><![CDATA[horizontal]]></data-element><data-element name="type"><![CDATA[combo]]></data-element><data-element name="mandatory"><![CDATA[1]]></data-element></data>',
    );
    $this->q->loadFromData($question);
    $this->q->setAnswers(
      array(
        10 => array('text' => 'ausgezeichnet', 'value' => '1'),
        11 => array('text' => 'sehr gut', 'value' => '2'),
        12 => array('text' => 'gut', 'value' => '3'),
        13 => array('text' => 'mittelmaessig', 'value' => '4'),
        14 => array('text' => 'schlecht', 'value' => '5'),
      )
    );

    $expected = '<question id="1" identifier="q4-1" key="1-1" alignment="horizontal" type="combo" mandatory="mandatory">
<text>Wie finden Sie diesen Test?</text>
<answers>
<answer id="answer_1" value="10" >ausgezeichnet</answer>
<answer id="answer_2" value="11" >sehr gut</answer>
<answer id="answer_3" value="12" selected="selected">gut</answer>
<answer id="answer_4" value="13" >mittelmaessig</answer>
<answer id="answer_5" value="14" >schlecht</answer>
</answers>
</question>
';

    $this->assertXmlStringEqualsXmlString($expected, $this->q->getQuestionXML(1, 12));
  }

  /**
  * @covers PapayaQuestionnaireQuestionSingleChoice5::isAnswerMandatory
  */
  public function testIsAnswerMandatory() {
    $this->q->loadFromData(array('mandatory' => 1));
    $this->assertTrue($this->q->isAnswerMandatory());
  }

}
