<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Question/SingleChoice10.php');

class PapayaQuestionnaireQuestionSingleChoice10Test extends PapayaTestCase {

  public function setUp() {
    $this->q = new PapayaQuestionnaireQuestionSingleChoice10;
    $this->q->setParamName('qpa');
  }

  public function testAttributes() {
    $this->assertTrue(count($this->readAttribute($this->q, '_answerFields')) == 10);
    $this->assertTrue(count($this->readAttribute($this->q, '_configurationFields')) == 26);
  }

}
