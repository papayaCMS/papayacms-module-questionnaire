<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

require_once(dirname(__FILE__).'/../../../src/Storage/Database/Access.php');
require_once(dirname(__FILE__).'/../../../src/Question/Abstract.php');
require_once(dirname(__FILE__).'/../../../src/Question/Creator.php');

class PapayaQuestionnaireStorageDatabaseAccessTest extends PapayaTestCase {

  public function setUp() {
    $this->defineConstantDefaults(
      array('DB_FETCHMODE_ASSOC')
    );
    $this->s = new PapayaQuestionnaireStorageDatabaseAccess();
    $this->s->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
  }

  public function getDatabaseAccessMock($methods = array()) {
    $mockDBAccess = $this->getMock(
      'PapayaDatabaseAccess',
      $methods,
      array(),
      'Mock_DB_Access_'.md5(uniqid()),
      FALSE
    );
    return $mockDBAccess;
  }

  /**
  * Load PapayaDatabaseAccess mock object fixture
  *
  * @param array $methods optional, default empty array
  * @return PapayaDatabaseAccess mock object
  */
  public function loadDatabaseObjectFixture($methods) {
    $defaultMethods = array('connect');
    $methods = array_unique(array_merge($defaultMethods, $methods));
    $databaseObject = $this->getMock(
      'PapayaDatabaseAccess',
      $methods,
      array(),
      'Mock_PapayaDatabaseAccess_'.md5(__CLASS__.microtime()),
      FALSE
    );
    $databaseObject
      ->expects($this->any())
      ->method('connect')
      ->will($this->returnValue(TRUE));
    return $databaseObject;
  }

  /**
  * Generate Mock object of a database result object
  *
  * @param array $methods List of methods to be mocked
  * @return dbresult_mysql mock object
  */
  private function loadDatabaseResultObjectFixture($methods) {
    if (!defined('DB_FETCHMODE_ASSOC')) {
      define('DB_FETCHMODE_ASSOC', 0);
    }
    return $this->getMock(
      'dbresult_mysql',
      $methods,
      array(),
      'Mock_dbresult_'.md5(__CLASS__.microtime()),
      FALSE
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::setConfiguration
  */
  public function testSetConfiguration() {
    $dba = new PapayaQuestionnaireStorageDatabaseAccess();
    $mockConfiguration = $this->getMockConfigurationObject();
    $dba->setConfiguration($mockConfiguration);
    $this->assertAttributeEquals($mockConfiguration, '_configuration', $dba);
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getConfiguration
  */
  public function testGetConfiguration() {
    $s = new PapayaQuestionnaireStorageDatabaseAccess();
    $mockConfiguration = $this->getMockConfigurationObject();
    $mockApplication = $this->getMockApplicationObject(array('options' => $mockConfiguration));
    $s->papaya($mockApplication);
    $this->assertEquals($mockConfiguration, $s->getConfiguration());
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::setTableNames
  */
  public function testSetTableNames() {
    $this->assertEquals(
      'papaya_questionnaire_pool',
      $this->readAttribute($this->s, '_tablePool')
    );
    $this->assertEquals(
      'papaya_questionnaire_group',
      $this->readAttribute($this->s, '_tableGroup')
    );
    $this->assertEquals(
      'papaya_questionnaire_question',
      $this->readAttribute($this->s, '_tableQuestion')
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getPools
  */
  public function testGetPools() {
    $expected = array(
      1 => array('question_pool_id' => 1, 'question_pool_name' => 'my pool', 'question_pool_identifier' => 'p1'),
      2 => array('question_pool_id' => 2, 'question_pool_name' => 'my other pool', 'question_pool_identifier' => 'p1')
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($expected[1]),
          $this->returnValue($expected[2]),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getPools());
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getPool
  */
  public function testGetPool() {
    $expected = array(
      array('question_pool_id' => 1, 'question_pool_name' => 'my pool', 'question_pool_identifier' => 'p1')
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->once())
      ->method('fetchRow')
      ->will($this->returnValue($expected));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::createPool
  */
  public function testCreatePool() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'insertRecord'));
    $mockDBAccess
      ->expects($this->once())
      ->method('insertRecord')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->createPool(array('question_pool_name' => 'my pool', 'question_pool_identifier' => 'p1')));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::updatePool
  */
  public function testUpdatePool() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'updateRecord'));
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue(
      $this->s->updatePool(
        1,
        array(
          'question_pool_name' => 'my pool',
        )
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::deletePool
  */
  public function testDeletePool() {
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('setDataModified', 'deleteRecord', 'escapeStr', 'masterOnly', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('deleteRecord')
      ->will($this->returnValue(0));
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->deletePool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::deleteQuestionsInPool
  */
  public function testDeleteQuestionsInPool() {
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'setDataModified', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->deleteQuestionsInPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::deleteGroupsInPool
  */
  public function testDeleteGroupsInPool() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'deleteRecord'));
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('deleteRecord')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->deleteGroupsInPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getGroups
  */
  public function testGetGroups() {
    $expected = array(
      1 => array(
        'question_group_id' => 1,
        'question_group_identifier' => 'g1',
        'question_group_name' => 'my group',
      ),
      2 => array(
        'question_group_id' => 2,
        'question_group_identifier' => 'g2',
        'question_group_name' => 'my other group',
      ),
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($expected[1]),
          $this->returnValue($expected[2]),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getGroups(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getGroup
  */
  public function testGetGroup() {
    $expected = array(
      'question_group_id' => 1,
      'question_group_identifier' => 'g1',
      'question_group_name' => 'my group',
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->once())
      ->method('fetchRow')
      ->will(
          $this->returnValue($expected)
        );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getGroup(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::createGroup
  */
  public function testCreateGroup() {
    $expected = array(0 => 1);
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockRes
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue($expected));
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('setDataModified', 'insertRecord', 'queryFmt', 'escapeStr', 'masterOnly')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('insertRecord')
      ->will($this->returnValue(TRUE));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue(
      $this->s->createGroup(
        array(
          'question_group_name' => 'my group',
          'question_group_subtitle' => 'my subtitle',
          'question_group_text' => 'some text information on this group',
          'question_group_identifier' => 'g1',
          'question_pool_id' => 1,
        )
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::updateGroup
  */
  public function testUpdateGroup() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'updateRecord'));
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue(
      $this->s->updateGroup(
        1,
        array(
          'question_group_name' => 'my group',
          'question_group_subtitle' => 'my subtitle',
          'question_group_text' => 'some text information on this group',
          'question_group_identifier' => 'g1',
          'question_pool_id' => 1
        )
      )
    );
  }

  public function testDeleteGroup() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'deleteRecord'));
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('deleteRecord')
      ->will($this->returnValue(0));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->deleteGroup(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_getGroupPoolId
  */
  public function testGetGroupPoolId() {
    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(TRUE));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));
    $proxyS = $this->getProxy(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_getGroupPoolId')
    );
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_getGroupPoolId(2));
  }

  public function testGetGroupPosition() {
    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(TRUE));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));
    $proxyS = $this->getProxy(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_getGroupPosition')
    );
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_getGroupPosition(2));
  }

  public function testGetNextGroupPosition() {
    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(2));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));
    $proxyS = $this->getProxy(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_getNextGroupPosition')
    );
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertEquals(3, $proxyS->_getNextGroupPosition(2));
  }

  public function testMoveGroupUp() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_moveGroup'));
    $mockS
      ->expects($this->once())
      ->method('_moveGroup')
      ->with($this->equalTo(3), $this->equalTo(-2))
      ->will($this->returnValue(TRUE));

    $this->assertTrue($mockS->moveGroupUp(3, 2));
  }

  public function testMoveGroupDown() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_moveGroup'));
    $mockS
      ->expects($this->once())
      ->method('_moveGroup')
      ->with($this->equalTo(3), $this->equalTo(4))
      ->will($this->returnValue(TRUE));

    $this->assertTrue($mockS->moveGroupDown(3, 4));
  }

  public function testMoveGroup() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_getGroupPosition', '_getGroupPoolId'));
    $mockS
      ->expects($this->once())
      ->method('_getGroupPosition')
      ->will($this->returnValue(2));
    $mockS
      ->expects($this->once())
      ->method('_getGroupPoolId')
      ->will($this->returnValue(3));
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'setDataModified', 'updateRecord', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->exactly(1))
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(1));

    $mockS->setDatabaseAccess($mockDBAccess);
    $mockS->_moveGroup(2, 1);
  }

  public function testMoveGroupNegative() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_getGroupPosition', '_getGroupPoolId'));
    $mockS
      ->expects($this->once())
      ->method('_getGroupPosition')
      ->will($this->returnValue(2));
    $mockS
      ->expects($this->once())
      ->method('_getGroupPoolId')
      ->will($this->returnValue(3));
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'setDataModified', 'updateRecord', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->exactly(1))
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(1));

    $mockS->setDatabaseAccess($mockDBAccess);
    $mockS->_moveGroup(2, -1);
  }


  public function testGetQuestions() {
    $expected = array(
      1 => array(
        'question_id' => 1,
        'question_group_id' => 3,
        'question_identifier' => 'q1',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The question is...',
        'question_answer_data' => '<data></data>',
      ),
      2 => array(
        'question_id' => 2,
        'question_group_id' => 3,
        'question_identifier' => 'q1',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The other question is...',
        'question_answer_data' => '<data></data>',
      )
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($expected[1]),
          $this->returnValue($expected[2]),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getQuestions(1));
  }


  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getQuestionsByIds
  */
  public function testGetQuestionsByIds() {
    $expected = array(
      1 => array(
        'question_id' => 1,
        'question_group_id' => 3,
        'question_identifier' => 'q1',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The question is...',
        'question_answer_data' => '<data></data>',
      ),
      2 => array(
        'question_id' => 2,
        'question_group_id' => 3,
        'question_identifier' => 'q1',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The other question is...',
        'question_answer_data' => '<data></data>',
      )
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($expected[1]),
          $this->returnValue($expected[2]),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt', 'getSQLCondition'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getQuestionsByIds(array(1, 2)));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getQuestionsByIdentifiers
  */
  public function testGetQuestionsByIdentifiers() {
    $expected = array(
      1 => array(
        'question_id' => 1,
        'question_group_id' => 3,
        'question_identifier' => 'q1',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The question is...',
        'question_answer_data' => '<data></data>',
      ),
      2 => array(
        'question_id' => 2,
        'question_group_id' => 3,
        'question_identifier' => 'q2',
        'question_type' => 'physician_appraisal',
        'question_text' => 'The other question is...',
        'question_answer_data' => '<data></data>',
      )
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($expected[1]),
          $this->returnValue($expected[2]),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt', 'getSQLCondition'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($expected, $this->s->getQuestionsByIdentifiers(array('q1', 'q2')));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getQuestion
  */
  public function testGetQuestion() {
    $dba = new PapayaQuestionnaireStorageDatabaseAccess();

    $expected = array(
      'question_id' => 1,
      'question_group_id' => 3,
      'question_identifier' => 'q1',
      'question_type' => 'single_choice_5',
      'question_text' => 'The question is...',
      'question_answer_data' => '<data></data>',
    );
    $expectedAnswers = array(1 => array('value' => 2, 'text' => 'text'));
    $answersData = array(
      'answer_choice_id' => 1,
      'answer_choice_value' => 2,
      'answer_choice_text' => 'text'
    );

    $mockQuestion = $this
      ->getMockBuilder('PapayaQuestionnaireQuestionAbstract')
      ->setMethods(array('loadFromData', 'setAnswers'))
      ->getMock();
    $mockQuestion
      ->expects($this->once())
      ->method('loadFromData')
      ->with($this->equalTo($expected));
    $mockQuestion
      ->expects($this->once())
      ->method('setAnswers')
      ->with($this->equalTo($expectedAnswers));
    $mockQuestionCreator = $this
      ->getMockBuilder('PapayaQuestionnaireQuestionCreator')
      ->setMethods(array('getQuestionObject'))
      ->getMock();
    $mockQuestionCreator
    ->expects($this->once())
      ->method('getQuestionObject')
      ->with('single_choice_5')
      ->will($this->returnValue($mockQuestion));
    $dba->setQuestionCreator($mockQuestionCreator);

    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->returnValue($expected));
    $mockResAnswers = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockResAnswers
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
        $this->onConsecutiveCalls(
          $this->returnValue($answersData),
          FALSE
        )
      );
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->at(0))
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $mockDBAccess
      ->expects($this->at(1))
      ->method('queryFmt')
      ->will($this->returnValue($mockResAnswers));
    $dba->setDatabaseAccess($mockDBAccess);

    $this->assertEquals($mockQuestion, $dba->getQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getQuestion
  * @expectedException LogicException
  */
  public function testGetQuestionFailureWithException() {
    $expected = array(
      'question_id' => 1,
      'question_group_id' => 3,
      'question_identifier' => 'q1',
      'question_type' => 'nonexisting-question-type',
      'question_text' => 'The question is...',
      'question_answer_data' => '<data></data>',
    );
    $question = new PapayaQuestionnaireQuestionAbstract;
    $question->loadFromData($expected);
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->returnValue($expected));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $mockS = $this->getMock('PapayaQuestionnaireStorageDatabaseAccess', array('_getQuestionAnswers'));
    $mockS->setDatabaseAccess($mockDBAccess);

    $this->assertInstanceOf('PapayaQuestionnaireQuestion', $mockS->getQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_getQuestionAnswers
  */
  public function testGetQuestionAnswers() {
    $expected = array(
      12 => array(
        'value' => 'value',
        'text' => 'Question text',
      )
    );
    $data = array(
      'answer_choice_text' => 'Question text',
      'answer_choice_value' => 'value',
      'answer_choice_id' => 12,
    );
    $mockRes = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockRes
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls($this->returnValue($data), $this->returnValue(FALSE)));
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'queryFmt'));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockRes));
    $proxyS = $this->getProxy('PapayaQuestionnaireStorageDatabaseAccess', array('_getQuestionAnswers'));
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertSame($expected, $proxyS->_getQuestionAnswers(1));

  }


  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_deleteQuestionAnswers
  */
  public function testDeleteQuestionAnswers() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('escapeStr', 'masterOnly', 'deleteRecord'));
    $mockDBAccess
      ->expects($this->once())
      ->method('deleteRecord')
      ->will($this->returnValue(TRUE));
    $proxyS = $this->getProxy('PapayaQuestionnaireStorageDatabaseAccess', array('_deleteQuestionAnswers'));
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_deleteQuestionAnswers(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::createQuestion
  */
  public function testCreateQuestion() {
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('setDataModified', 'insertRecord', 'escapeStr', 'masterOnly', 'query')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('insertRecord')
      ->will($this->returnValue(TRUE));
    $proxyS = $this->getProxy('PapayaQuestionnaireStorageDatabaseAccess', array('_getNextQuestionPosition'));
    $mockS = $this->getMock(get_class($proxyS), array('_getNextQuestionPosition', '_saveQuestionAnswers'));
    $mockS
      ->expects($this->once())
      ->method('_getNextQuestionPosition')
      ->will($this->returnValue(1));
    $mockS
      ->expects($this->once())
      ->method('_saveQuestionAnswers')
      ->will($this->returnValue(TRUE));
    $mockS->setDatabaseAccess($mockDBAccess);

    $question = new PapayaQuestionnaireQuestionAbstract();
    $question->loadFromData(array(
      'question_group_id' => 3,
      'question_identifier' => 'q1',
      'question_type' => 'single_choice_5',
      'question_text' => 'The other question is...',
      'question_answer_data' => '<data></data>',
      'question_position' => 4,
    ));
    $this->assertTrue($mockS->createQuestion($question));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::updateQuestion
  */
  public function testUpdateQuestion() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'updateRecord'));
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $mockS = $this->getMock(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_saveQuestionAnswers'));
    $mockS
      ->expects($this->once())
      ->method('_saveQuestionAnswers')
      ->will($this->returnValue(TRUE));
    $mockS->setDatabaseAccess($mockDBAccess);

    $data = array(
      'question_id' => 3,
      'question_identifier' => 'q3',
      'question_pool_id' => 1,
      'question_text' => 'What is the question?',
      'question_type' => 'single_choice_5',
      'question_answer_data' => '<data></data>',
    );
    $questionObject = new PapayaQuestionnaireQuestionAbstract;
    $questionObject->loadFromData($data);

    $this->assertTrue(
      $mockS->updateQuestion($questionObject)
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_saveQuestionAnswers
  * @dataProvider provideSaveQustionAnswersData
  */
  public function testSaveQuestionAnswers($oldAnswers, $answers, $dbMethod, $dbParam1, $dbParam2 = NULL) {
    $proxyS = $this->getProxy('PapayaQuestionnaireStorageDatabaseAccess',
      array('_getQuestionAnswers', '_saveQuestionAnswers'));
    $mockS = $this->getMock(get_class($proxyS), array('_getQuestionAnswers'));

    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestionAbstract', array('getId', 'getAnswers'));
    $mockQuestion
      ->expects($this->atLeastOnce())
      ->method('getId')
      ->will($this->returnValue(3));
    $mockQuestion
      ->expects($this->once())
      ->method('getAnswers')
      ->will($this->returnValue($answers));

    $mockDBAccess = $this->loadDatabaseObjectFixture(array('insertRecord', 'deleteRecord', 'updateRecord'));
    if ($dbParam2) {
      $mockDBAccess
        ->expects($this->once())
        ->method($dbMethod)
        ->with(
          $this->equalTo($this->readAttribute($mockS, '_tableAnswerOptions')),
          $this->equalTo($dbParam1),
          $this->equalTo($dbParam2)
        )
        ->will($this->returnValue(TRUE));
    } else {
      $mockDBAccess
        ->expects($this->once())
        ->method($dbMethod)
        ->with(
          $this->equalTo($this->readAttribute($mockS, '_tableAnswerOptions')),
          $this->equalTo($dbParam1)
        )
        ->will($this->returnValue(TRUE));
    }
    $mockS
      ->expects($this->once())
      ->method('_getQuestionAnswers')
      ->will($this->returnValue($oldAnswers));

    $mockS->setDatabaseAccess($mockDBAccess);

    $mockS->_saveQuestionAnswers($mockQuestion);
  }

  public function provideSaveQustionAnswersData() {
    return array(
      'insert new answer' => array(
        array(
        ),
        array(
          1 => array(
            'text' => 'another answer text',
            'value' => 'another value',
          ),
        ),
        'insertRecord',
        NULL,
        array(
          'answer_choice_text' => 'another answer text',
          'answer_choice_value' => 'another value',
          'question_id' => 3,
        ),

      ),
      'update existing answer' => array(
        array(
          7 => array(
            'text' => 'the answer text',
            'value' => 'some value',
          ),
        ),
        array(
          1 => array(
            'text' => 'the altered answer text',
            'value' => 'some value',
          ),
        ),
        'updateRecord',
        array(
          'answer_choice_text' => 'the altered answer text',
          'answer_choice_value' => 'some value',
        ),
        array(
          'answer_choice_id' => 7
        ),
      ),
      'delete existing answer' => array(
        array(
          7 => array(
            'text' => 'the answer text',
            'value' => 'some value',
          ),
          8 => array(
            'text' => 'answer to be deleted',
            'value' => 'a value',
          ),
        ),
        array(
          1 => array(
            'text' => 'the answer text',
            'value' => 'some value',
          ),
        ),
        'deleteRecord',
        // condition in data
        array(
          'answer_choice_id' => 8
        ),
      ),
    );
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::deleteQuestion
  */
  public function testDeleteQuestion() {
    $mockDBAccess = $this->getDatabaseAccessMock(array('setDataModified', 'deleteRecord'));
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('deleteRecord')
      ->will($this->returnValue(0));
    $this->s->setDatabaseAccess($mockDBAccess);

    $this->assertTrue($this->s->deleteQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_getQuestionGroupId
  */
  public function testGetQuestionGroupId() {
    $proxyS = $this->getProxy('PapayaQuestionnaireStorageDatabaseAccess', array('_getQuestionGroupId'));

    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(3));

    $mockDBAccess = $this->getDatabaseAccessMock(array('queryFmt'));
    $mockDBAccess
      ->expects($this->atLeastOnce())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));
    $proxyS->setDatabaseAccess($mockDBAccess);

    $this->assertSame(3, $proxyS->_getQuestionGroupId(1));

  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_getQuestionPosition
  */
  public function testGetQuestionPosition() {
    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(TRUE));
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'queryFmt')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));

    $proxyS = $this->getProxy(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_getQuestionPosition')
    );
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_getQuestionPosition(2));
  }

  public function testGetNextQuestionPosition() {
    $mockResult = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $mockResult
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(2));
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'queryFmt')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockResult));
    $proxyS = $this->getProxy(
      'PapayaQuestionnaireStorageDatabaseAccess',
      array('_getNextQuestionPosition')
    );
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertEquals(3, $proxyS->_getNextQuestionPosition(2));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::moveQuestionUp
  */
  public function testMoveQuestionUp() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_moveQuestion'));
    $mockS
      ->expects($this->once())
      ->method('_moveQuestion')
      ->with($this->equalTo(2), $this->equalTo(-1))
      ->will($this->returnValue(TRUE));

    $this->assertTrue($mockS->moveQuestionUp(2));

  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::moveQuestionDown
  */
  public function testMoveQuestionDown() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $mockS = $this->getMock(get_class($proxyS), array('_moveQuestion'));
    $mockS
      ->expects($this->once())
      ->method('_moveQuestion')
      ->with($this->equalTo(2), $this->equalTo(1))
      ->will($this->returnValue(TRUE));

    $this->assertTrue($mockS->moveQuestionDown(2));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_moveQuestion
  */
  public function testMoveQuestionWithPositiveRelativePosition() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'setDataModified', 'updateRecord', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_moveQuestion(2, 1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::_moveQuestion
  */
  public function testMoveQuestionWithNegativeRelativePosition() {
    $proxyS = new QuestionnaireStorageDatabaseAccessProxy();
    $proxyS->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );

    $expectedUpdateQuery = '';
    $mockDBAccess = $this->getDatabaseAccessMock(
      array('escapeStr', 'masterOnly', 'query', 'setDataModified', 'updateRecord', 'queryFmtWrite')
    );
    $mockDBAccess
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $mockDBAccess
      ->expects($this->once())
      ->method('queryFmtWrite')
      ->will($this->returnValue(TRUE));
    $proxyS->setDatabaseAccess($mockDBAccess);
    $this->assertTrue($proxyS->_moveQuestion(2, -1));
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::setQuestionCreator
  */
  public function testSetQuestionCreator() {
    $mockCreator = $this->getMock('PapayaQuestionnaireQuestionCreator');
    $this->s->setQuestionCreator($mockCreator);
    $this->assertAttributeSame($mockCreator, '_questionCreator', $this->s);
  }

  /**
  * @covers PapayaQuestionnaireStorageDatabaseAccess::getQuestionCreator
  */
  public function testGetQuestionCreator() {
    $this->assertInstanceOf('PapayaQuestionnaireQuestionCreator', $this->s->getQuestionCreator());
  }
}

class QuestionnaireStorageDatabaseAccessProxy extends PapayaQuestionnaireStorageDatabaseAccess {
  public function _getGroupPosition($groupId) {
    return 2;
  }

  public function _getQuestionGroupId($groupId) {
    return 2;
  }

  public function _getQuestionPosition($questionId) {
    return 2;
  }

  public function _moveGroup($groupId, $relativePosition) {
    return parent::_moveGroup($groupId, $relativePosition);
  }

  public function _moveQuestion($questionId, $relativePosition) {
    return parent::_moveQuestion($questionId, $relativePosition);
  }
}
