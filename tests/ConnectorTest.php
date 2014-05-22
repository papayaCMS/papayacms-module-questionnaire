<?php
require_once(dirname(__FILE__).'/bootstrap.php');

require_once(dirname(__FILE__).'/../src/Connector.php');
require_once(dirname(__FILE__).'/../src/Storage.php');
require_once(dirname(__FILE__).'/../src/Storage/Copier.php');
require_once(dirname(__FILE__).'/../src/Question/Creator.php');
require_once(dirname(__FILE__).'/../src/Question/Abstract.php');
require_once(dirname(__FILE__).'/../src/Answer.php');

class PapayaQuestionnaireConnectorTest extends PapayaTestCase {

  public function setUp() {
    if (!defined('PAPAYA_DB_TABLEPREFIX')) {
      define('PAPAYA_DB_TABLEPREFIX', 'papaya');
    }
    if (!defined('PAPAYA_DB_TBL_MODULEOPTIONS')) {
      define('PAPAYA_DB_TBL_MODULEOPTIONS', 'moduleoptions');
    }
    $this->c = new PapayaQuestionnaireConnector_TestProxy();
    $this->c->setConfiguration(
      $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::setConfiguration
  */
  public function testSetConfiguration() {
    $connector = new PapayaQuestionnaireConnector();
    $mockConfiguration = $this->getMockConfigurationObject();
    $connector->setConfiguration($mockConfiguration);
    $this->assertAttributeEquals($mockConfiguration, '_configuration', $connector);
  }

  /**
  * @covers PapayaQuestionnaireConnector::setStorage
  */
  public function testSetStorage() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage');
    $this->c->setStorage($mockStorage);
    $this->assertSame($mockStorage, $this->readAttribute($this->c, '_storage'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getStorage
  */
  public function testGetStorage() {
    $storage = $this->c->getStorage();
    $this->assertTrue($storage instanceof PapayaQuestionnaireStorage);
  }

  /**
  * @covers PapayaQuestionnaireConnector::setQuestionCreator
  */
  public function testSetQuestionCreator() {
    $mockQCreator = $this->getMock('PapayaQuestionnaireQuestionCreator');
    $this->c->setQuestionCreator($mockQCreator);
    $this->assertSame($mockQCreator, $this->readAttribute($this->c, '_creator'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestionCreator
  */
  public function testGetQuestionCreator() {
    $creator = $this->c->getQuestionCreator();
    $this->assertTrue($creator instanceof PapayaQuestionnaireQuestionCreator);
  }

  /**
  * @covers PapayaQuestionnaireConnector::setAnswerObject
  */
  public function testSetAnswerObject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $this->c->setAnswerObject($answerObject);
    $this->assertSame($answerObject, $this->readAttribute($this->c, '_answerObject'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getAnswerObject
  */
  public function testGetAnswerObject() {
    $answerObject = $this->c->getAnswerObject();
    $this->assertTrue($answerObject instanceof PapayaQuestionnaireAnswer);
  }

  /**
  * @covers PapayaQuestionnaireConnector::getPools
  */
  public function testGetPools() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getPools'));
    $mockStorage
      ->expects($this->once())
      ->method('getPools')
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getPools());
  }

  /**
  * @covers PapayaQuestionnaireConnector::getPool
  */
  public function testGetPool() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getPool'));
    $mockStorage
      ->expects($this->once())
      ->method('getPool')
      ->with($this->equalTo(1))
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getPool(1));
  }

  /**
  * @covers PapayaQuestionnaireConnector::createPool
  */
  public function testCreatePool() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('createPool'));
    $mockStorage
      ->expects($this->once())
      ->method('createPool');
    $this->c->setStorage($mockStorage);
    $this->c->createPool(array());
  }

  /**
  * @covers PapayaQuestionnaireConnector::updatePool
  */
  public function testUpdatePool() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('updatePool'));
    $mockStorage
      ->expects($this->once())
      ->method('updatePool');
    $this->c->setStorage($mockStorage);
    $this->c->updatePool(1, array());
  }

  /**
  * @covers PapayaQuestionnaireConnector::setCopier
  */
  public function testSetCopier() {
    $mockCopier = $this->getMock('PapayaQuestionnaireStorageCopier', array('copyPool'));
    $this->assertTrue($this->c->setCopier($mockCopier));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getCopier
  */
  public function testGetCopier() {
    $this->assertTrue($this->c->getCopier() instanceof PapayaQuestionnaireStorageCopier);
  }

  /**
  * @covers PapayaQuestionnaireConnector::copyPool
  */
  public function testCopyPool() {
    $mockCopier = $this->getMock('PapayaQuestionnaireStorageCopier', array('copyPool'));
    $mockCopier
      ->expects($this->once())
      ->method('copyPool')
      ->with($this->equalTo(2));
    $this->c->setCopier($mockCopier);
    $this->c->copyPool(2);
  }

  /**
  * @covers PapayaQuestionnaireConnector::deletePool
  */
  public function testDeletePool() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('deletePool'));
    $mockStorage
      ->expects($this->once())
      ->method('deletePool');
    $this->c->setStorage($mockStorage);
    $this->c->deletePool(1);
  }

  /**
  * @covers PapayaQuestionnaireConnector::getGroups
  */
  public function testGetGroups() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getGroups'));
    $mockStorage
      ->expects($this->once())
      ->method('getGroups')
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getGroups(1));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getGroup
  */
  public function testGetGroup() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getGroup'));
    $mockStorage
      ->expects($this->once())
      ->method('getGroup')
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getGroup(1));
  }

  /**
  * @covers PapayaQuestionnaireConnector::createGroup
  */
  public function testCreateGroup() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('createGroup'));
    $mockStorage
      ->expects($this->once())
      ->method('createGroup');
    $this->c->setStorage($mockStorage);
    $this->c->createGroup(array());
  }

  /**
  * @covers PapayaQuestionnaireConnector::updateGroup
  */
  public function testUpdateGroup() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('updateGroup'));
    $mockStorage
      ->expects($this->once())
      ->method('updateGroup')
      ->with($this->equalTo(1), $this->equalTo(array()));
    $this->c->setStorage($mockStorage);
    $this->c->updateGroup(1, array());
  }

  /**
  * @covers PapayaQuestionnaireConnector::deleteGroup
  */
  public function testDeleteGroup() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('deleteGroup'));
    $mockStorage
      ->expects($this->once())
      ->method('deleteGroup');
    $this->c->setStorage($mockStorage);
    $this->c->deleteGroup(1);
  }

  /**
  * @covers PapayaQuestionnaireConnector::moveGroupUp
  */
  public function testMoveGroupUp() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('moveGroupUp'));
    $mockStorage
      ->expects($this->once())
      ->method('moveGroupUp');
    $this->c->setStorage($mockStorage);
    $this->c->moveGroupUp(2);
  }

  /**
  * @covers PapayaQuestionnaireConnector::moveGroupDown
  */
  public function testMoveGroupDown() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('moveGroupDown'));
    $mockStorage
      ->expects($this->once())
      ->method('moveGroupDown');
    $this->c->setStorage($mockStorage);
    $this->c->moveGroupDown(2);
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestions
  */
  public function testGetQuestions() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getQuestions'));
    $mockStorage
      ->expects($this->once())
      ->method('getQuestions')
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getQuestions(1));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestionsByIds
  */
  public function testGetQuestionsByIds() {
    $expected = array(
      1 => array(
        'question_id' => 1,
        'question_identifier' => 'q1',
      ),
      3 => array(
        'question_id' => 3,
        'question_identifier' => 'q3',
      ),
    );
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getQuestionsByIds'));
    $mockStorage
      ->expects($this->once())
      ->method('getQuestionsByIds')
      ->with($this->equalTo(array(1, 3)))
      ->will($this->returnValue($expected));
    $this->c->setStorage($mockStorage);
    $this->assertEquals($expected, $this->c->getQuestionsByIds(array(1, 3)));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestionsByIdentifiers
  */
  public function testGetQuestionsByIdentifiers() {
    $expected = array(
      1 => array(
        'question_id' => 1,
        'question_identifier' => 'q1',
        // ...
      ),
      3 => array(
        'question_id' => 3,
        'question_identifier' => 'q3',
      ),
    );
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getQuestionsByIdentifiers'));
    $mockStorage
      ->expects($this->once())
      ->method('getQuestionsByIdentifiers')
      ->with($this->equalTo(array('q1', 'q3')))
      ->will($this->returnValue($expected));
    $this->c->setStorage($mockStorage);
    $this->assertEquals($expected, $this->c->getQuestionsByIdentifiers(array('q1', 'q3')));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestion
  */
  public function testGetQuestion() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('getQuestion'));
    $mockStorage
      ->expects($this->once())
      ->method('getQuestion')
      ->will($this->returnValue(TRUE));
    $this->c->setStorage($mockStorage);

    $this->assertTrue($this->c->getQuestion(1));
  }

  /**
  * @covers PapayaQuestionnaireConnector::createQuestion
  */
  public function testCreateQuestion() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('createQuestion'));
    $mockStorage
      ->expects($this->once())
      ->method('createQuestion');
    $this->c->setStorage($mockStorage);
    $this->c->createQuestion(array());
  }

  /**
  * @covers PapayaQuestionnaireConnector::updateQuestion
  */
  public function testUpdateQuestion() {
    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestionAbstract');
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('updateQuestion'));
    $mockStorage
      ->expects($this->once())
      ->method('updateQuestion')
      ->with($this->equalTo($mockQuestion));
    $this->c->setStorage($mockStorage);
    $this->c->updateQuestion($mockQuestion);
  }

  /**
  * @covers PapayaQuestionnaireConnector::deleteQuestion
  */
  public function testDeleteQuestion() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('deleteQuestion'));
    $mockStorage
      ->expects($this->once())
      ->method('deleteQuestion');
    $this->c->setStorage($mockStorage);
    $this->c->deleteQuestion(1);
  }

  /**
  * @covers PapayaQuestionnaireConnector::moveQuestionUp
  */
  public function testMoveQuestionUp() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('moveQuestionUp'));
    $mockStorage
      ->expects($this->once())
      ->method('moveQuestionUp');
    $this->c->setStorage($mockStorage);
    $this->c->moveQuestionUp(2);
  }

  /**
  * @covers PapayaQuestionnaireConnector::moveQuestionDown
  */
  public function testMoveQuestionDown() {
    $mockStorage = $this->getMock('PapayaQuestionnaireStorage', array('moveQuestionDown'));
    $mockStorage
      ->expects($this->once())
      ->method('moveQuestionDown');
    $this->c->setStorage($mockStorage);
    $this->c->moveQuestionDown(2);
  }

  /**
  * @covers PapayaQuestionnaireConnector::getQuestionObject
  */
  public function testGetQuestionObject() {
    $mockQuestion = $this->getMock('PapayaQuestionnaireQuestionAbstract');
    $mockQCreator = $this->getMock('PapayaQuestionnaireQuestionCreator', array('getQuestionObject'));
    $mockQCreator
      ->expects($this->once())
      ->method('getQuestionObject')
      ->with($this->equalTo('question_type'))
      ->will($this->returnValue($mockQuestion));
    $this->c->setQuestionCreator($mockQCreator);
    $this->assertSame($mockQuestion, $this->c->getQuestionObject('question_type'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getActiveAnswersByUserAndSubject
  */
  public function testGetActiveAnswersByUserAndSubject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answers = array(1 => 3, 2 => 1, 3 => 4);
    $answerObject
      ->expects($this->once())
      ->method('getActiveAnswersByUserAndSubject')
      ->will($this->returnValue($answers));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      $answers,
      $this->c->getActiveAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
   * @covers PapayaQuestionnaireConnector::getAnswers
   */
  public function testGetAnswers() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answers = array(1 => array('question_id' => 1), 2 => array('question_id' => 2));
    $filter = array('answer_set_id' => 'answer_set_id_1');
    $answerObject
      ->expects($this->once())
      ->method('getAnswers')
      ->with($this->equalTo($filter))
      ->will($this->returnValue($answers));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals($answers, $this->c->getAnswers($filter));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getAnswersByUserAndSubject
  */
  public function testGetAnswersByUserAndSubject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answers = array(
      1 => array('answer_value' => 3, 'answer_deactivated' => 0),
      2 => array('answer_value' => 1, 'answer_deactivated' => 0),
      3 => array('answer_value' => 4, 'answer_deactivated' => 1234567890)
    );
    $answerObject
      ->expects($this->once())
      ->method('getAnswersByUserAndSubject')
      ->will($this->returnValue($answers));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      $answers,
      $this->c->getAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::getActiveAnswersBySubjectId
  */
  public function testGetActiveAnswersBySubjectId() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answers = array(
      1 => array('user_id_1' => 3, 'user_id_2' => 4),
      2 => array('user_id_1' => 1, 'user_id_2' => 3),
      3 => array('user_id_1' => 4, 'user_id_2' => 4)
    );
    $answerObject
      ->expects($this->once())
      ->method('getActiveAnswersBySubjectId')
      ->will($this->returnValue($answers));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals($answers, $this->c->getActiveAnswersBySubjectId('subject-id', FALSE));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getActiveSubjectIdsByUser
  */
  public function testGetActiveSubjectIdsByUser() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $subjectIds = array(
      'subject_id_6' => 1234567890,
      'subject_id_7' => 1234567891,
      'subject_id_42' => 1234567892
    );
    $answerObject
      ->expects($this->once())
      ->method('getActiveSubjectIdsByUser')
      ->will($this->returnValue($subjectIds));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals($subjectIds, $this->c->getActiveSubjectIdsByUser('user-id'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getSubjectIdsByUser
  */
  public function testGetSubjectIdsByUser() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $subjects = array(
      'subject_id_6' => array(
        'subject_id' => 'subject_id_6',
        'answer_timestamp' => 1234467898,
        'answer_deactivated' => 1234467899,
      ),
      'subject_id_7' => array(
        'subject_id' => 'subject_id_7',
        'answer_timestamp' => 1234567898,
        'answer_deactivated' => 1234567899,
      )
    );
    $answerObject
      ->expects($this->once())
      ->method('getSubjectIdsByUser')
      ->will($this->returnValue($subjects));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals($subjects, $this->c->getSubjectIdsByUser('user-id'));
  }

  /**
   * @covers PapayaQuestionnaireConnector::getAnswerSets
   */
  public function testGetAnswerSets() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer', array('getAnswerSets'));
    $answerSets = array(
      'answer_set_id_6' => array(
        'answer_set_id' => 'answer_set_id_6',
        'subject_id' => '4711',
        'answer_timestamp' => 1234467898,
        'answer_deactivated' => 1234467899,
      ),
      'answer_set_id_7' => array(
        'answer_set_id' => 'answer_set_id_7',
        'subject_id' => '42',
        'answer_timestamp' => 1234567898,
        'answer_deactivated' => 1234567899,
      )
    );
    $answerObject
      ->expects($this->once())
      ->method('getAnswerSets')
      ->with(array('user_id' => 'user-id'), 'DESC')
      ->will($this->returnValue($answerSets));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      $answerSets,
      $this->c->getAnswerSets(array('user_id' => 'user-id'), 'DESC')
    );
  }

  /**
   * @covers PapayaQuestionnaireConnector::markAnswerSetDeleted
   */
  public function testMarkAnswerSetDeleted() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer', array('markAnswerSetDeleted'));
    $answerObject
      ->expects($this->once())
      ->method('markAnswerSetDeleted')
      ->with($this->equalTo('answerset-id'))
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(TRUE, $this->c->markAnswerSetDeleted('answerset-id'));

  }

  /**
   * @covers PapayaQuestionnaireConnector::getAnswerOptions
   */
  public function testGetAnswerOptions() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer', array('getAnswerOptions'));
    $options = array(
      11 => array(
        'question_id' => '1',
        'answer_choice_id' => '11',
        'answer_choice_text' => 'Kind',
        'answer_choice_value' => 'child',
      ),
      12 => array(
        'question_id' => '1',
        'answer_choice_id' => '12',
        'answer_choice_text' => 'Selbst',
        'answer_choice_value' => 'self',
      ),
    );
    $answerObject
      ->expects($this->once())
      ->method('getAnswerOptions')
      ->with(array('question_id' => '1'))
      ->will($this->returnValue($options));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      $options,
      $this->c->getAnswerOptions(array('question_id' => '1'))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::getAnswerTimestampByUserAndSubject
  */
  public function testGetAnswerTimestampByUserAndSubject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('getAnswerTimestampByUserAndSubject')
      ->will($this->returnValue(1234567890));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      1234567890,
      $this->c->getAnswerTimestampByUserAndSubject('user-id', 'subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::saveAnswersByUserAndSubject
  */
  public function testSaveAnswersByUserAndSubject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('saveAnswersByUserAndSubject')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->saveAnswersByUserAndSubject('user-id', 'subject-id', array(1 => 3, 2 => 1, 3 => 4))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::saveAnswersOfUserForSubject
  */
  public function testSaveAnswersOfUserForSubject() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('saveAnswersOfUserForSubject')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->saveAnswersOfUserForSubject('user-id', 'subject-id', array(1 => 3, 2 => 1, 3 => 4))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::deactivateAnswersByUserId
  */
  public function testDeactivateAnswersByUserId() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('deactivateAnswersByUserId')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->deactivateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::activateAnswersByUserId
  */
  public function testActivateAnswersByUserId() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('activateAnswersByUserId')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->activateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::deactivateAnswersBySubjectId
  */
  public function testDeactivateAnswersBySubjectId() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('deactivateAnswersBySubjectId')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->deactivateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::activateAnswersBySubjectId
  */
  public function testActivateAnswersBySubjectId() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('activateAnswersBySubjectId')
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->activateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::addMetaForAnswerSet
  */
  public function testAddMetaForAnswerSet() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('addMetaForAnswerSet')
      ->with($this->equalTo(1), $this->equalTo('key'), $this->equalTo('value'))
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue($this->c->addMetaForAnswerSet(1, 'key', 'value'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getMetaForAnswerSet
  */
  public function testGetMetaForAnswerSet() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('getMetaForAnswerSet')
      ->with($this->equalTo(1), $this->equalTo('key'))
      ->will($this->returnValue('value'));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals('value', $this->c->getMetaForAnswerSet(1, 'key'));
  }

  /**
  * @covers PapayaQuestionnaireConnector::moduleOptions
  */
  public function testModuleOptionsGet() {
    $this->assertInstanceOf('base_module_options', $this->c->moduleOptions());
  }

  /**
  * @covers PapayaQuestionnaireConnector::moduleOptions
  */
  public function testModuleOptionsSet() {
    $moduleOptions = $this->getMock('base_module_options');
    $this->assertSame($moduleOptions, $this->c->moduleOptions($moduleOptions));
  }

  /**
  * @covers PapayaQuestionnaireConnector::getAnswerSetLimit
  */
  public function testGetAnswerSetLimit() {
    $moduleOptions = $this->getMock('base_module_options', array('readOption'));
    $moduleOptions
      ->expects($this->once())
      ->method('readOption')
      ->with($this->equalTo('36d94a3fdaf122d8214776b34ffdb012'), $this->equalTo('answerset_limit'))
      ->will($this->returnValue(0));
    $this->c->moduleOptions($moduleOptions);
    $this->assertEquals(12, $this->c->getAnswerSetLimit());
  }

  /**
  * @covers PapayaQuestionnaireConnector::getCurrentYearAnswerSets
  */
  public function testGetCurrentYearAnswerSets() {

    $expected = array('12345', '67890');

    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');
    $answerObject
      ->expects($this->once())
      ->method('getAnswerSetsInCurrentYear')
      ->with($this->equalTo('12345abcd'))
      ->will($this->returnValue(array('12345', '67890')));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      $expected,
      $this->c->getCurrentYearAnswerSets('12345abcd')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::getAnswerSetsForSurfers
  */
  public function testGetAnswerSetsForSurfers() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');

    $answerObject
      ->expects($this->once())
      ->method('getAnswerSetsForSurfers')
      ->with($this->equalTo(array('Surfer_1', 'Surfer_2')))
      ->will($this->returnValue(array(1234,5678)));
    $this->c->setAnswerObject($answerObject);
    $this->assertEquals(
      array(1234, 5678),
      $this->c->getAnswerSetsForSurfers(array('Surfer_1', 'Surfer_2'))
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::deleteDeprecatedAnswerSets
  */
  public function testDeleteDeprecatedAnswerSets() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');

    $answerObject
      ->expects($this->once())
      ->method('deleteDeprecatedAnswerSets')
      ->with($this->equalTo(array(1234, 5678)), $this->equalTo('Surfer_1'))
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->deleteDeprecatedAnswerSets(array(1234,5678), 'Surfer_1')
    );
  }

  /**
  * @covers PapayaQuestionnaireConnector::transferAnswerSetsToSurfer
  */
  public function testTransferAnswerSetsToSurfer() {
    $answerObject = $this->getMock('PapayaQuestionnaireAnswer');

    $answerObject
      ->expects($this->once())
      ->method('transferAnswerSetsToSurfer')
      ->with($this->equalTo(array(1234, 5678)), $this->equalTo('Surfer_1'))
      ->will($this->returnValue(TRUE));
    $this->c->setAnswerObject($answerObject);
    $this->assertTrue(
      $this->c->transferAnswerSetsToSurfer(array(1234,5678), 'Surfer_1')
    );
  }
}

class PapayaQuestionnaireConnector_TestProxy extends PapayaQuestionnaireConnector {
  public function __construct() {
    // Just override constructor
  }
}
