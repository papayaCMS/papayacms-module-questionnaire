<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Dialog/Message.php');

class PapayaQuestionnaireDialogMessageTest extends PapayaTestCase {

  public function testCreateDialog() {
    $msgDialog = new PapayaQuestionnaireDialogMessageProxy;
    $dialog = $msgDialog->createDialog($this, 'b', array(), 'message', 'info');
    $this->assertTrue($dialog instanceof base_msgdialog);
  }

}


class PapayaQuestionnaireDialogMessageProxy extends PapayaQuestionnaireDialogMessage {
  public function getBaseLink() {
    return TRUE;
  }

  public function getApplication() {
    return new PapayaQuestionnaireDialogMessageApplicationMock;
  }
}

class PapayaQuestionnaireDialogMessageApplicationMock {
  public function hasObject() {
    return TRUE;
  }
}
