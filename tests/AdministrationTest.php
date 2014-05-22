<?php
require_once(dirname(__FILE__).'/bootstrap.php');

require_once(dirname(__FILE__).'/../src/Administration.php');
require_once(dirname(__FILE__).'/../src/Administration/Output.php');
require_once(dirname(__FILE__).'/../src/Storage/Database/Converter.php');
require_once(dirname(__FILE__).'/../src/Connector.php');
require_once(dirname(__FILE__).'/../src/Question/Abstract.php');
require_once(dirname(__FILE__).'/../src/Question/Creator.php');

require_once(PAPAYA_INCLUDE_PATH.'/system/base_dialog.php');

class PapayaQuestionnaireAdministrationTest extends PapayaTestCase {

  public function setUp() {
    $this->defineConstantDefaults(array(
      'PAPAYA_DB_TABLEPREFIX',
      'PAPAYA_DB_TBL_MODULES',
      'PAPAYA_XSLT_EXTENSION',
      'PAPAYA_URL_LEVEL_SEPARATOR',
    ));
    $this->a = new QuestionnaireAdministrationProxy();
    $mockConfiguration = $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'));
    $this->a->setConfiguration($mockConfiguration);
    $mockApp = $this->getMockApplicationObject();
    $this->a->setApplication($mockApp);
    $mockCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionTypes'));
    $mockCreator
      ->expects($this->any())
      ->method('getQuestionTypes')
      ->will($this->returnValue(array('question_type' => 'Question type name')));
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector');
    $mockConnector
      ->expects($this->any())
      ->method('getQuestionCreator')
      ->will($this->returnValue($mockCreator));
    $mockConnector->setConfiguration($mockConfiguration);
    $this->a->setConnector($mockConnector);
  }

  public function getMessageManagerMock() {
    $msgManager = $this->getMock('PapayaMessageManager', array('addMsg'));
    $msgManager
      ->expects($this->any())
      ->method('addMsg');
    return $msgManager;
  }

  public function testSetPluginLoader() {
    $mockPluginLoader = $this->getMock('base_pluginloader', array('getPluginInstance'));
    $this->a->setPluginLoader($mockPluginLoader);
    $this->assertSame($mockPluginLoader, $this->readAttribute($this->a, '_pluginLoader'));
  }


  /**
  * @covers PapayaQuestionnaireAdministration::initialize
  */
  public function testInitialize() {
    $a = $this->getMock(
      'PapayaQuestionnaireAdministration',
      array(
        'initializeParams',
        'getSessionValue',
        'initializeSessionParam',
        'setSessionValue',
        '_initializeConnector',
        '_initializeDialogObject'
      )
    );
    $a->initialize();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::_initializePluginLoader
  */
  public function testInitializePluginLoader() {
    $a = new QuestionnaireAdministrationPublic;
    $a->_initializePluginLoader();
    $this->assertTrue($this->readAttribute($a, '_pluginLoader') instanceof base_pluginloader);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::_initializeConnector
  */
  public function testInitializeConnector() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector');
    $mockPluginLoader = $this->getMock('base_pluginloader', array('getPluginInstance'));
    $mockPluginLoader
      ->expects($this->once())
      ->method('getPluginInstance')
      ->with($this->equalTo('36d94a3fdaf122d8214776b34ffdb012'))
      ->will($this->returnValue($mockConnector));

    $a = new QuestionnaireAdministrationPublic;
    $a->setPluginLoader($mockPluginLoader);
    $a->_initializeConnector();
    $this->assertTrue($this->readAttribute($a, '_connector') instanceof PapayaQuestionnaireConnector);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::_initializeDialogObject
  */
  public function testInitializeDialogObject() {
    $a = $this->getMock('QuestionnaireAdministrationPublic', array('setDialogObject'));
    $a
      ->expects($this->once())
      ->method('setDialogObject')
      ->with($this->isInstanceOf('PapayaQuestionnaireAdministrationDialogs'))
      ->will($this->returnValue(TRUE));
    $a->_initializeDialogObject();
  }

  public function testSetOutputObject() {
    $mockOutput = $this->getMock('PapayaQuestionnaireAdministrationOutput');
    $this->a->setOutputObject($mockOutput);
    $this->assertAttributeSame($mockOutput, '_output', $this->a);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::_initializeOutputObject
  */
  public function testInitializeOutputObject() {
    $a = new QuestionnaireAdministrationPublic;
    $a->images = array();
    $a->_initializeOutputObject();
    $this->assertTrue($this->readAttribute($a, '_output') instanceof PapayaQuestionnaireAdministrationOutput);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::setConfiguration
  */
  public function testSetConfiguration() {
    $mockConfig = $this->getMockConfigurationObject();
    $this->a->setConfiguration($mockConfig);
    $this->assertSame($mockConfig, $this->readAttribute($this->a, '_configuration'));
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddPool
  */
  public function testProcessAddPoolDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getPoolDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getPoolDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'add_pool');
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddPool
  */
  public function testProcessAddPool() {
    $parameters = array(
      'cmd' => 'add_pool',
      'submit' => 1,
      'question_pool_name' => 'my pool name',
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createPool'));
    $mockConnector
      ->expects($this->once())
      ->method('createPool')
      ->with($this->equalTo($parameters))
      ->will($this->returnValue(3));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddPool
  */
  public function testProcessAddPoolFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createPool'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processAddPool'));
    $aProxy->params = array('submit' => 1);
    $aProxy->setConnector($mockConnector);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processAddPool());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditPool
  */
  public function testProcessEditPoolDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock_'.md5(microtime(TRUE)), FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getPoolDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getPoolDialog')
      ->with($this->equalTo(array('POOL_DATA')))
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockConnector = $this->getMock('PapayaQuestionnaireStorageDatabaseAccess', array('getPool'));
    $mockConnector
      ->expects($this->once())
      ->method('getPool')
      ->with($this->equalTo(2))
      ->will($this->returnValue(array('POOL_DATA')));
    $this->a->setConnector($mockConnector);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'edit_pool', 'question_pool_id' => 2);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditPool
  */
  public function testProcessEditPool() {
    $parameters = array(
      'cmd' => 'edit_pool',
      'submit' => 1,
      'question_pool_name' => 'my pool name',
      'question_pool_id' => 2,
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('updatePool'));
    $mockConnector
      ->expects($this->once())
      ->method('updatePool')
      ->with($this->equalTo(2), $this->equalTo($parameters))
      ->will($this->returnValue(TRUE));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditPool
  */
  public function testProcessEditPoolUpdateFail() {
    $parameters = array(
      'cmd' => 'edit_pool',
      'submit' => 1,
      'question_pool_name' => 'my pool name',
      'question_pool_id' => 2,
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('updatePool'));
    $mockConnector
      ->expects($this->once())
      ->method('updatePool')
      ->with($this->equalTo(2), $this->equalTo($parameters))
      ->will($this->returnValue(FALSE));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processCopyPool
  */
  public function testProcessCopyPool() {
    $parameters = array(
      'cmd' => 'copy_pool',
      'question_pool_id' => 3,
    );
    $mockCopier = $this->getMock('PapayaQuestionnaireStorageCopier', array('copyPool'));
    $mockCopier
      ->expects($this->once())
      ->method('copyPool')
      ->with($this->equalTo(3))
      ->will($this->returnValue(5));
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('getCopier'));
    $mockConnector
      ->expects($this->once())
      ->method('getCopier')
      ->will($this->returnValue($mockCopier));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processCopyPool
  */
  public function testProcessCopyPoolFail() {
    $parameters = array(
      'cmd' => 'copy_pool',
      'question_pool_id' => 3,
    );
    $mockCopier = $this->getMock('PapayaQuestionnaireStorageCopier', array('copyPool'));
    $mockCopier
      ->expects($this->once())
      ->method('copyPool')
      ->with($this->equalTo(3))
      ->will($this->returnValue(FALSE));
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('getCopier'));
    $mockConnector
      ->expects($this->once())
      ->method('getCopier')
      ->will($this->returnValue($mockCopier));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeletePool
  */
  public function testDeletePoolDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getMsgDialog'), array(), 'base_dialog_mock_2', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getMsgDialog')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getDeletePoolDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getDeletePoolDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'del_pool', 'question_pool_id' => 2);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeletePool
  */
  public function testDeletePool() {
    $parameters = array(
      'cmd' => 'del_pool',
      'submit' => 1,
      'question_pool_id' => 2,
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deletePool'));
    $mockConnector
      ->expects($this->once())
      ->method('deletePool')
      ->with($this->equalTo(2))
      ->will($this->returnValue(TRUE));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeletePool
  */
  public function testProcessDeletePoolFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deletePool'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processDeletePool'));
    $aProxy->params = array('submit' => 1, 'question_pool_id' => 1);
    $aProxy->setConnector($mockConnector);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processDeletePool());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddGroup
  */
  public function testProcessAddGroupDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock_3', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getGroupDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getGroupDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'add_group', 'question_pool_id' => 1);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddGroup
  */
  public function testProcessAddGroup() {
    $parameters = array(
      'cmd' => 'add_group',
      'submit' => 1,
      'question_pool_id' => 2,
      'question_group_name' => 'my test group',
      'question_group_identifier' => 'g1',
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createGroup'));
    $mockConnector
      ->expects($this->once())
      ->method('createGroup')
      ->with($this->equalTo($parameters))
      ->will($this->returnValue(3));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddGroup
  */
  public function testProcessAddGroupFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createGroup'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processAddGroup'));
    $aProxy->setConnector($mockConnector);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processAddGroup());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddGroup
  */
  public function testProcessAddGroupFailCreate() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createGroup'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processAddGroup'));
    $aProxy->setConnector($mockConnector);
    $aProxy->params = array('submit' => 1, 'question_pool_id' => 1);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processAddGroup());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditGroup
  */
  public function testProcessEditGroupDialog() {
    $group = array(
      'question_pool_id' => 2,
      'question_group_name' => 'my test group',
      'question_group_text' => 'some text',
      'question_group_identifier' => 'g1',
    );
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock_10', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getGroupDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getGroupDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('getGroup'));
    $mockConnector
      ->expects($this->once())
      ->method('getGroup')
      ->with($this->equalTo(1))
      ->will($this->returnValue($group));
    $this->a->setConnector($mockConnector);

    $this->a->params = array('cmd' => 'edit_group', 'question_group_id' => 1);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditGroup
  */
  public function testProcessEditGroup() {
    $parameters = array(
      'cmd' => 'edit_group',
      'submit' => 1,
      'question_group_id' => 2,
      'question_group_name' => 'my test group',
      'question_group_identifier' => 'g1',
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('updateGroup'));
    $mockConnector
      ->expects($this->once())
      ->method('updateGroup')
      ->with($this->equalTo(2), $this->equalTo($parameters))
      ->will($this->returnValue(3));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditGroup
  */
  public function testProcessEditGroupFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createGroup'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processEditGroup'));
    $aProxy->setConnector($mockConnector);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processEditGroup());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeleteGroup
  */
  public function testDeleteGroupDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getMsgDialog'), array(), 'base_dialog_mock_4', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getMsgDialog')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getDeleteGroupDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getDeleteGroupDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'del_group', 'question_group_id' => 2);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeleteGroup
  */
  public function testDeleteGroup() {
    $parameters = array(
      'cmd' => 'del_group',
      'submit' => 1,
      'question_group_id' => 2,
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deleteGroup'));
    $mockConnector
      ->expects($this->once())
      ->method('deleteGroup')
      ->with($this->equalTo(2))
      ->will($this->returnValue(TRUE));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeleteGroup
  */
  public function testProcessDeleteGroupFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deleteGroup'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processDeleteGroup'));
    $aProxy->setConnector($mockConnector);
    $aProxy->params = array('submit' => 1, 'question_group_id' => 2);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processDeleteGroup());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddQuestion
  */
  public function testProcessAddQuestionDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock_5', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getQuestionDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getQuestionDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockQuestionCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionObject'));
    $mockQuestionCreator
      ->expects($this->once())
      ->method('getQuestionObject')
      ->will($this->returnValue(NULL));


    $this->a->setQuestionCreator($mockQuestionCreator);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'add_question', 'question_group_id' => 4);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddQuestion
  */
  public function testProcessAddQuestion() {
    $parameters = array(
      'cmd' => 'add_question',
      'submit' => 1,
      'question_group_id' => 6,
      'question_text' => 'my question name',
      'question_identifier' => 'q1',
      'question_answer_data' => '<data></data>',
      'question_type' => 'physician_appraisal',
    );

    $question = new PapayaQuestionnaireQuestionAbstract();
    $question->loadFromData($parameters);

    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('createQuestion')
      ->with($this->equalTo($question))
      ->will($this->returnValue(3));

    $mockQuestionCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionObject'));
    $mockQuestionCreator
      ->expects($this->once())
      ->method('getQuestionObject')
      ->will($this->returnValue($question));


    $this->a->setConnector($mockConnector);
    $this->a->setQuestionCreator($mockQuestionCreator);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddQuestion
  */
  public function testProcessAddQuestionFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createQuestion'));

    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processAddQuestion'));
    $aProxy->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);

    $this->assertFalse($aProxy->_processAddQuestion());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processAddQuestion
  */
  public function testProcessAddQuestionFailCreate() {
    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestion');
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processAddQuestion'));
    $aMock = $this->getMock(get_class($aProxy), array('_getQuestionObject'));
    $aMock
      ->expects($this->once())
      ->method('_getQuestionObject')
      ->will($this->returnValue($mockQuestion));

    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('createQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('createQuestion')
      ->with($this->equalTo($mockQuestion))
      ->will($this->returnValue(FALSE));

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aMock->setApplication($mockApp);

    $aMock->setConnector($mockConnector);
    $aMock->params = array('submit' => 1, 'question_group_id' => 1, 'question_type' => 'physician_appraisal');
    $this->assertFalse($aMock->_processAddQuestion());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditQuestion
  */
  public function testProcessEditQuestionDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getDialogXML'), array(), 'base_dialog_mock_20', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getDialogXML')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getQuestionDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getQuestionDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $question = $this->getMock('PapayaQuestionnaireQuestion');
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('getQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('getQuestion')
      ->will($this->returnValue($question));
    $this->a->setConnector($mockConnector);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'edit_question', 'question_id' => 6);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditQuestion
  */
  public function testProcessEditQuestion() {
    $parameters = array(
      'cmd' => 'edit_question',
      'question_id' => 3,
      'submit' => 1,
      'question_group_id' => 6,
      'question_text' => 'my question name',
      'question_identifier' => 'q1',
      'answer_1' => 1,
      'answer_2' => 2,
      'question_type' => 'physician_appraisal',
    );
    $answerParameters = $parameters;
    $answerParameters['question_answer_data'] = array();
    $question = new PapayaQuestionnaireQuestionAbstract;
    $question->loadFromData($answerParameters);
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('updateQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('updateQuestion')
      ->with($this->equalTo($question))
      ->will($this->returnValue(TRUE));
    $mockQuestionCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionObject'));
    $mockQuestionCreator
      ->expects($this->once())
      ->method('getQuestionObject')
      ->with($this->equalTo('physician_appraisal'))
      ->will($this->returnValue($question));

    $this->a->setConnector($mockConnector);
    $this->a->setQuestionCreator($mockQuestionCreator);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processEditQuestion
  */
  public function testProcessEditQuestionFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('updateQuestion'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processEditQuestion'));
    $aProxy->setConnector($mockConnector);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processEditQuestion());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::setQuestionCreator
  */
  public function testSetQuestionCreator() {
    $mockCreator = $this->getMock('PapayaQuestionnaireQuestionCreator');
    $this->a->setQuestionCreator($mockCreator);
    $this->assertAttributeEquals($mockCreator, '_questionCreator', $this->a);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::getQuestionCreator
  */
  public function testGetQuestionCreator() {
    $this->assertTrue($this->a->getQuestionCreator() instanceof PapayaQuestionnaireQuestionCreator);
  }

  public function testDeleteQuestionDialog() {
    $mockDialog = $this->getMock('base_dialog', array('getMsgDialog'), array(), 'base_dialog_mock_6', FALSE, FALSE, FALSE);
    $mockDialog
      ->expects($this->once())
      ->method('getMsgDialog')
      ->will($this->returnValue('<dialog></dialog>'));
    $mockDialogObj = $this->getMock('PapayaQuestionnaireAdministrationDialogs', array('getDeleteQuestionDialog'));
    $mockDialogObj
      ->expects($this->once())
      ->method('getDeleteQuestionDialog')
      ->will($this->returnValue($mockDialog));
    $this->a->setDialogObject($mockDialogObj);

    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->once())
      ->method('add')
      ->will($this->returnValue(TRUE));
    $this->a->setLayoutObject($mockLayout);

    $this->a->params = array('cmd' => 'del_question', 'question_id' => 2);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeleteQuestion
  */
  public function testProcessDeleteQuestionFail() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deleteQuestion'));
    $aProxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_processDeleteQuestion'));
    $aProxy->setConnector($mockConnector);
    $aProxy->params = array('submit' => 1, 'question_id' => 2);
    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $aProxy->setApplication($mockApp);
    $this->assertFalse($aProxy->_processDeleteQuestion());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processDeleteQuestion
  */
  public function testDeleteQuestion() {
    $parameters = array(
      'cmd' => 'del_question',
      'submit' => 1,
      'question_id' => 2,
    );
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('deleteQuestion'));
    $mockConnector
      ->expects($this->once())
      ->method('deleteQuestion')
      ->with($this->equalTo(2))
      ->will($this->returnValue(TRUE));
    $this->a->setConnector($mockConnector);

    $mockApp = $this->getMockApplicationObject(array('messages' => $this->getMessageManagerMock()));
    $this->a->setApplication($mockApp);

    $this->a->params = $parameters;
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processMoveGroupUp
  */
  public function testProcessMoveGroupUp() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('moveGroupUp'));
    $mockConnector
      ->expects($this->once())
      ->method('moveGroupUp')
      ->will($this->returnValue(TRUE));
    $this->a->params = array('question_group_id' => 1, 'cmd' => 'group_up');
    $this->a->setConnector($mockConnector);
    $this->assertTrue($this->a->execute());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processMoveGroupDown
  */
  public function testProcessMoveGroupDown() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('moveGroupDown'));
    $mockConnector
      ->expects($this->once())
      ->method('moveGroupDown')
      ->will($this->returnValue(TRUE));
    $this->a->params = array('question_group_id' => 1, 'cmd' => 'group_down');
    $this->a->setConnector($mockConnector);
    $this->assertTrue($this->a->execute());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processMoveQuestionUp
  */
  public function testProcessMoveQuestionUp() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('moveQuestionUp'));
    $mockConnector
      ->expects($this->once())
      ->method('moveQuestionUp')
      ->will($this->returnValue(TRUE));
    $this->a->params = array('question_id' => 1, 'cmd' => 'question_up');
    $this->a->setConnector($mockConnector);
    $this->assertTrue($this->a->execute());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processMoveQuestionDown
  */
  public function testProcessMoveQuestionDown() {
    $mockConnector = $this->getMock('PapayaQuestionnaireConnector', array('moveQuestionDown'));
    $mockConnector
      ->expects($this->once())
      ->method('moveQuestionDown')
      ->will($this->returnValue(TRUE));
    $this->a->params = array('question_id' => 1, 'cmd' => 'question_down');
    $this->a->setConnector($mockConnector);
    $this->assertTrue($this->a->execute());
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processOverview
  */
  public function testProcessOverview() {
    $testXml = '<sheet></sheet>';
    $testHtml = '<html></html>';
    $mockOverview = $this->getMock(
      'PapayaQuestionnaireOverview',
      array('getXml', 'getHtml', 'setPrintLink', 'getResponseObject'),
      array(),
      '',
      FALSE
    );
    $mockOverview
      ->expects($this->exactly(2))
      ->method('getXml')
      ->will($this->returnValue($testXml));
    $mockOverview
      ->expects($this->once())
      ->method('getHtml')
      ->will($this->returnValue($testHtml));
    $mockOverview
      ->expects($this->exactly(2))
      ->method('setPrintLink');
    $this->a->setOverviewObject($mockOverview);
    $mockLayout = $this->getMock('papaya_xsl', array('add'));
    $mockLayout
      ->expects($this->exactly(2))
      ->method('add')
      ->with($this->equalTo($testXml));
    $this->a->setLayoutObject($mockLayout);
    $this->a->params = array('cmd' => 'overview');
    $this->a->execute();

    $mockResponse = $this->getMock('PapayaResponse');
    $this->a->setResponseObject($mockResponse);
    $this->a->params = array('cmd' => 'overview', 'print' => 1);
    $this->a->execute();

    $this->a->params = array('cmd' => 'overview_extended');
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::execute
  * @covers PapayaQuestionnaireAdministration::_processMigrateAnswers
  */
  public function testProcessMigrateAnswers() {
    $mockConverter = $this->getMock(
      'PapayaQuestionnaireStorageDatabaseConverter',
      array('convertXmlAnswerStructure')
    );
    $mockConverter
      ->expects($this->once())
      ->method('convertXmlAnswerStructure')
      ->will($this->returnValue(TRUE));
    $this->a->params = array('cmd' => 'migrate_answers');
    $this->a->setConverterObject($mockConverter);
    $this->a->execute();
  }

  /**
  * @covers PapayaQuestionnaireAdministration::setConverterObject
  */
  public function testSetConverter() {
    $mockConverter = $this->getMock('PapayaQuestionnaireStorageDatabaseConverter');
    $this->a->setConverterObject($mockConverter);
    $this->assertAttributeSame($mockConverter, '_converter', $this->a);
  }

  /**
  * @covers PapayaQuestionnaireAdministration::getConverterObject
  */
  public function testGetConverter() {
    $this->assertInstanceOf('PapayaQuestionnaireStorageDatabaseConverter', $this->a->getConverterObject());
  }


  /**
  * @covers PapayaQuestionnaireAdministration::_getQuestionObject
  */
  public function testGetQuestionObject() {
    $params = array('question_type' => 'question-type');
    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('loadFromData'));
    $mockQuestion
      ->expects($this->once())
      ->method('loadFromData')
      ->with($this->equalTo($params));
    $mockCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionObject'));
    $mockCreator
      ->expects($this->once())
      ->method('getQuestionObject')
      ->with($this->equalTo('question-type'))
      ->will($this->returnValue($mockQuestion));
    $proxy = $this->getProxy('PapayaQuestionnaireAdministration', array('_getQuestionObject'));
    $proxy->setQuestionCreator($mockCreator);
    $this->assertSame($mockQuestion, $proxy->_getQuestionObject($params));
  }




  /**
  * @covers PapayaQuestionnaireAdministration::getXML
  */
  public function testGetXML() {
    $this->a->images = array();
    $this->a->params = array(
      'question_pool_id' => 1,
      'question_group_id' => 2,
    );
    $mockOutput = $this->getMock(
      'PapayaQuestionnaireAdministrationOutput',
      array('getToolbar', 'getPoolList', 'getGroupList', 'getQuestionList'));
    $mockOutput
      ->expects($this->once())
      ->method('getToolbar')
      ->will($this->returnValue('<menu></menu>'));
    $mockOutput
      ->expects($this->once())
      ->method('getPoolList')
      ->will($this->returnValue('<listview></listview>'));
    $mockOutput
      ->expects($this->once())
      ->method('getGroupList')
      ->will($this->returnValue('<listview></listview>'));
    $mockOutput
      ->expects($this->once())
      ->method('getQuestionList')
      ->will($this->returnValue('<listview></listview>'));

    $mockLayout = $this->getMock('papaya_xsl', array('addLeft', 'addMenu'));
    $mockLayout
      ->expects($this->exactly(3))
      ->method('addLeft')
      ->will($this->returnValue(TRUE));
    $mockLayout
      ->expects($this->once())
      ->method('addMenu')
      ->will($this->returnValue(TRUE));

    $mockConnector = $this->getMock(
      'PapayaQuestionnaireConnector',
      array('getPools', 'getGroups', 'getQuestions'));
    $mockConnector
      ->expects($this->once())
      ->method('getPools')
      ->will($this->returnValue(TRUE));
    $mockConnector
      ->expects($this->once())
      ->method('getGroups')
      ->will($this->returnValue(TRUE));
    $mockConnector
      ->expects($this->once())
      ->method('getQuestions')
      ->will($this->returnValue(TRUE));


    $this->a->setOutputObject($mockOutput);
    $this->a->setLayoutObject($mockLayout);
    $this->a->setConnector($mockConnector);
    $this->a->getXML();
  }

}

class QuestionnaireAdministrationPublic extends PapayaQuestionnaireAdministration {
  public function _initializeConnector() {
    parent::_initializeConnector();
  }

  public function _initializeDialogObject() {
    parent::_initializeDialogObject();
  }

  public function _initializePluginLoader() {
    parent::_initializePluginLoader();
  }

  public function _initializeOutputObject() {
    parent::_initializeOutputObject();
  }
}

class QuestionnaireAdministrationProxy extends QuestionnaireAdministrationPublic {
  public $images = array();

  public function initializeParams() {
    return TRUE;
  }

  public function getSessionValue($key) {
    return 'sessionValue';
  }

  public function setSessionValue($key, $value) {
    $this->sessionValues[$key] = $value;
  }

  public function addMsg($level, $msg) {
    // Noting to do here, just override the original method
  }
}
