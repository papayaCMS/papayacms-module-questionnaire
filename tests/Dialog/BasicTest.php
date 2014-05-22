<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Dialog/Basic.php');

class PapayaQuestionnaireDialogBasicTest extends PapayaTestCase {

  public function testCreateDialog() {
    $basicDialog = new PapayaQuestionnaireDialogBasicProxy;
    $dialog = $basicDialog->createDialog($this, 'b', array(), array(), array());
    $this->assertTrue($dialog instanceof base_dialog);
  }

}

class PapayaQuestionnaireDialogBasicProxy extends PapayaQuestionnaireDialogBasic {
  public function getBaseLink() {
    return TRUE;
  }

  public function getApplication() {
    return new PapayaQuestionnaireDialogBasicApplicationMock;
  }
}

class PapayaQuestionnaireDialogBasicApplicationMock {
  public function hasObject() {
    return TRUE;
  }
}
