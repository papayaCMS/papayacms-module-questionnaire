<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Dialog/Builder.php');

class PapayaQuestionnaireDialogBuilderTest extends PapayaTestCase {

  public function testCreateDialog() {
    $builder = new PapayaQuestionnaireDialogBuilder;
    $dialog = $builder->createDialog($this, 'b', array(), array(), array());
    $this->assertTrue($dialog instanceof base_dialog);
  }

  public function testCreateMsgDialog() {
    $builder = new PapayaQuestionnaireDialogBuilder;
    $dialog = $builder->createMsgDialog($this, 'b', array(), 'message', 'question');
    $this->assertTrue($dialog instanceof base_msgdialog);
  }
}
