<?php
require_once(dirname(__FILE__).'/bootstrap.php');

require_once(dirname(__FILE__).'/../src/Storage.php');
require_once(dirname(__FILE__).'/../src/Question.php');
require_once(dirname(__FILE__).'/../src/Storage/Database/Access.php');

class PapayaQuestionnaireStorageTest extends PapayaTestCase {

  public function setUp() {
    $this->s = new PapayaQuestionnaireStorage();
    $configuration = $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'));
    $this->s->setConfiguration($configuration);
  }

  public function getQuestionMock() {
    return $this->getMock('PapayaQuestionnaireQuestion');
  }

  /**
  * Load PapayaQuestionnaireStorageDatabaseAccess mock object fixture
  *
  * @return PapayaQuestionnaireStorageDatabaseAccess mock object
  */
  private function loadDatabaseAccessObjectFixture() {
    return $this->getMock('PapayaQuestionnaireStorageDatabaseAccess');
  }

  /**
  * @covers PapayaQuestionnaireStorage::setConfiguration
  */
  public function testSetConfiguration() {
    $storage = new PapayaQuestionnaireStorage();
    $mockConfiguration = $this->getMockConfigurationObject();
    $storage->setConfiguration($mockConfiguration);
    $this->assertAttributeEquals($mockConfiguration, '_configuration', $storage);
  }

  /**
  * @covers PapayaQuestionnaireStorage::setDatabaseAccessObject
  */
  public function testSetDatabaseAccessObject() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertSame(
      $databaseAccessObject,
      $this->readAttribute($this->s, '_databaseAccessObject')
    );
  }

  /**
  * @covers PapayaQuestionnaireStorage::getDatabaseAccessObject
  */
  public function testGetDatabaseAccessObject() {
    $databaseAccessObject = $this->s->getDatabaseAccessObject();
    $this->assertTrue($databaseAccessObject instanceof PapayaQuestionnaireStorageDatabaseAccess);
  }

  /**
  * @covers PapayaQuestionnaireStorage::getPools
  */
  public function testGetPools() {
    $expected = array(
      1 => array('question_pool_id' => 1, 'question_pool_name' => 'my pool'),
      2 => array('question_pool_id' => 2, 'question_pool_name' => 'my other pool')
    );
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getPools')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getPools());
  }

  /**
  * @covers PapayaQuestionnaireStorage::getPool
  */
  public function testGetPool() {
    $expected = array(array('question_pool_id' => 1, 'question_pool_name' => 'my pool'));
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getPool')
      ->with($this->equalTo(1))
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::createPool
  */
  public function testCreatePool() {
    $mockDB = $this->getMock('base_db', array('databaseInsertRecord'));
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('createPool')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->createPool(array('question_pool_name' => 'my pool')));
  }

  /**
  * @covers PapayaQuestionnaireStorage::updatePool
  */
  public function testUpdatePool() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('updatePool')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
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
  * @covers PapayaQuestionnaireStorage::deletePool
  */
  public function testDeletePool() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deletePool')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deletePool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::deleteQuestionsInGroup
  */
  public function testDeleteQuestionsInGroup() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteQuestionsInGroup')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deleteQuestionsInGroup(1));
  }
  /**
  * @covers PapayaQuestionnaireStorage::deleteQuestionsInPool
  */
  public function testDeleteQuestionsInPool() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteQuestionsInPool')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deleteQuestionsInPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::deleteGroupsInPool
  */
  public function testDeleteGroupsInPool() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteGroupsInPool')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deleteGroupsInPool(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::getGroups
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
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getGroups')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getGroups(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::getGroup
  */
  public function testGetGroup() {
    $expected = array(
      'question_group_id' => 1,
      'question_group_identifier' => 'g1',
      'question_group_name' => 'my group',
    );
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getGroup')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getGroup(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::createGroup
  */
  public function testCreateGroup() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('createGroup')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $this->s->createGroup(
        array(
          'question_group_name' => 'my group',
          'question_group_text' => 'some text information on this group',
          'question_group_identifier' => 'g1',
          'question_pool_id' => 1,
        )
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireStorage::updateGroup
  */
  public function testUpdateGroup() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('updateGroup')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $this->s->updateGroup(
        1,
        array(
          'question_group_name' => 'my group',
          'question_group_text' => 'some text information on this group',
          'question_group_identifier' => 'g1',
          'question_pool_id' => 1,
        )
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireStorage::deleteGroup
  */
  public function testDeleteGroup() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteGroup')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deleteGroup(1));
  }

  public function testMoveGroupUp() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('moveGroupUp')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->moveGroupUp(2));
  }

  public function testMoveGroupDown() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('moveGroupDown')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->moveGroupDown(2));
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
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getQuestions')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getQuestions(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::getQuestionsByIds
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
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getQuestionsByIds')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getQuestionsByIds(array(1, 2)));
  }

  /**
  * @covers PapayaQuestionnaireStorage::getQuestionsByIdentifiers
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
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getQuestionsByIdentifiers')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getQuestionsByIdentifiers(array('q1', 'q2')));
  }

  /**
  * @covers PapayaQuestionnaireStorage::getQuestion
  */
  public function testGetQuestion() {
    $expected = array(
      'question_id' => 1,
      'question_group_id' => 3,
      'question_identifier' => 'q1',
      'question_type' => 'physician_appraisal',
      'question_text' => 'The question is...',
      'question_answer_data' => '<data></data>',
    );
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getQuestion')
      ->will($this->returnValue($expected));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($expected, $this->s->getQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::createQuestion
  */
  public function testCreateQuestion() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('createQuestion')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $mockQuestion = $this->getQuestionMock();
    $this->assertTrue($this->s->createQuestion($mockQuestion));
  }

  /**
  * @covers PapayaQuestionnaireStorage::updateQuestion
  */
  public function testUpdateQuestion() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('updateQuestion')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $mockQuestion = $this->getQuestionMock();
    $this->assertTrue($this->s->updateQuestion($mockQuestion));
  }

  /**
  * @covers PapayaQuestionnaireStorage::deleteQuestion
  */
  public function testDeleteQuestion() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteQuestion')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->deleteQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireStorage::moveQuestionUp
  */
  public function testMoveQuestionUp() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('moveQuestionUp')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->moveQuestionUp(2));
  }

  /**
  * @covers PapayaQuestionnaireStorage::moveQuestionDown
  */
  public function testMoveQuestionDown() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('moveQuestionDown')
      ->will($this->returnValue(TRUE));
    $this->s->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($this->s->moveQuestionDown(2));
  }
}
