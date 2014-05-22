<?php
require_once(dirname(__FILE__).'/bootstrap.php');

require_once(dirname(__FILE__).'/../src/Answer.php');
require_once(dirname(__FILE__).'/../src/Answer/Database/Access.php');

class PapayaQuestionnaireAnswerTest extends PapayaTestCase {
  /**
  * Load PapayaQuestionnaireAnswer oject fixture
  *
  * @return PapayaQuestionnaireAnswer
  */
  private function loadQuestionnaireAnswerObjectFixture() {
    $answerObject = new PapayaQuestionnaireAnswer();
    $configuration = $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'));
    $answerObject->setConfiguration($configuration);
    return $answerObject;
  }

  /**
  * Load PapayaQuestionnaireAnswerDatabaseAccess mock object fixture
  *
  * @return PapayaQuestionnaireAnswerDatabaseAccess mock object
  */
  private function loadDatabaseAccessObjectFixture() {
    return $this->getMock('PapayaQuestionnaireAnswerDatabaseAccess');
  }

  /**
  * @covers PapayaQuestionnaireAnswer::setDatabaseAccessObject
  */
  public function testSetDatabaseAccessObject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertSame($databaseAccessObject, $this->readAttribute($answerObject, '_databaseAccessObject'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getDatabaseAccessObject
  */
  public function testGetDatabaseAccessObject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $answerObject->getDatabaseAccessObject();
    $this->assertTrue($databaseAccessObject instanceof PapayaQuestionnaireAnswerDatabaseAccess);
  }

  /**
  * @covers PapayaQuestionnaireAnswer::setConfiguration
  */
  public function testSetConfiguration() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $mockConfiguration = $this->getMockConfigurationObject();
    $answerObject->setConfiguration($mockConfiguration);
    $this->assertAttributeEquals($mockConfiguration, '_configuration', $answerObject);
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getActiveAnswersByUserAndSubject
  */
  public function testGetActiveAnswersByUserAndSubject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $answers = array(1 => 3, 2 => 1, 3 => 4);
    $databaseAccessObject
      ->expects($this->once())
      ->method('getActiveAnswersByUserAndSubject')
      ->will($this->returnValue($answers));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      $answers,
      $answerObject->getActiveAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswer::getAnswers
   */
  public function testGetAnswers() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $answers = array(1 => array('question_id' => 1), 2 => array('question_id' => 2));
    $filter = array('answer_set_id' => 'answer_set_id_1');
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswers')
      ->with($this->equalTo($filter))
      ->will($this->returnValue($answers));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($answers, $answerObject->getAnswers($filter));
  }

  /**
   * @covers PapayaQuestionnaireAnswer::getAnswerOptions
   */
  public function testGetAnswerOptions() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $options = array(1 => array('answer_choice_id' => 1), 2 => array('answer_choice_id' => 2));
    $filter = array('question_id' => 1);
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswerOptions')
      ->with($this->equalTo($filter))
      ->will($this->returnValue($options));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($options, $answerObject->getAnswerOptions($filter));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getAnswersByUserAndSubject
  */
  public function testGetAnswersByUserAndSubject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $answers = array(
      1 => array('answer_value' => 3, 'answer_deactivated' => 0),
      2 => array('answer_value' => 1, 'answer_deactivated' => 0),
      3 => array('answer_value' => 4, 'answer_deactivated' => 1234567890)
    );
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswersByUserAndSubject')
      ->will($this->returnValue($answers));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      $answers,
      $answerObject->getAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getActiveAnswersBySubjectId
  */
  public function testGetActiveAnswersBySubjectId() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $answers = array(
      1 => array('user_id_1' => 3, 'user_id_2' => 4),
      2 => array('user_id_1' => 1, 'user_id_2' => 3),
      3 => array('user_id_1' => 4, 'user_id_2' => 4)
    );
    $databaseAccessObject
      ->expects($this->once())
      ->method('getActiveAnswersBySubjectId')
      ->will($this->returnValue($answers));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($answers, $answerObject->getActiveAnswersBySubjectId('subject-id', FALSE));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getActiveSubjectIdsByUser
  */
  public function testGetActiveSubjectIdsByUser() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $subjectIds = array(
      'subject_id_6' => 1234567890,
      'subject_id_7' => 1234567891,
      'subject_id_42' => 1234567892
    );
    $databaseAccessObject
      ->expects($this->once())
      ->method('getActiveSubjectIdsByUser')
      ->will($this->returnValue($subjectIds));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($subjectIds, $answerObject->getActiveSubjectIdsByUser('user-id'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getSubjectIdsByUser
  */
  public function testGetSubjectIdsByUser() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
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
    $databaseAccessObject
      ->expects($this->once())
      ->method('getSubjectIdsByUser')
      ->will($this->returnValue($subjects));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($subjects, $answerObject->getSubjectIdsByUser('user-id'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getAnswerSets
  */
  public function testGetAnswerSets() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
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
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswerSets')
      ->with(array('user_id' => 'user-id'), 'DESC')
      ->will($this->returnValue($answerSets));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      $answerSets,
      $answerObject->getAnswerSets(array('user_id' => 'user-id'), 'DESC')
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswer::markAnswerSetDeleted
   */
  public function testMarkAnswerSetDeleted() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('markAnswerSetDeleted')
      ->with($this->equalTo('answerset-id'))
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(TRUE, $answerObject->markAnswerSetDeleted('answerset-id'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getActiveSubjectIdsByMetaKeyValue
  */
  public function testGetActiveSubjectIdsByMetaKeyValue() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $subjectIds = array(
      'subject_id_6' => 1234567890,
      'subject_id_7' => 1234567891,
      'subject_id_42' => 1234567892
    );
    $databaseAccessObject
      ->expects($this->once())
      ->method('getActiveSubjectIdsByMetaKeyValue')
      ->will($this->returnValue($subjectIds));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals($subjectIds, $answerObject->getActiveSubjectIdsByMetaKeyValue('user-id', 'test'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getAnswerTimestampByUserAndSubject
  */
  public function testGetAnswerTimestampByUserAndSubject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswerTimestampByUserAndSubject')
      ->will($this->returnValue(1234567890));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      1234567890,
      $answerObject->getAnswerTimestampByUserAndSubject('user-id', 'subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::saveAnswersByUserAndSubject
  */
  public function testSaveAnswersByUserAndSubject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('saveAnswersByUserAndSubject')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->saveAnswersByUserAndSubject(
        'user-id',
        'subject-id',
        array(1 => 3, 2 => 1, 3 => 4)
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::saveAnswersOfUserForSubject
  */
  public function testSaveAnswersOfUserForSubject() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('saveAnswersOfUserForSubject')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->saveAnswersOfUserForSubject(
        'user-id',
        'subject-id',
        array(1 => 3, 2 => 1, 3 => 4)
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::deactivateAnswersByUserId
  */
  public function testDeactivateAnswersByUserId() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deactivateAnswersByUserId')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->deactivateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::activateAnswersByUserId
  */
  public function testActivateAnswersByUserId() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('activateAnswersByUserId')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->activateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::deactivateAnswersBySubjectId
  */
  public function testDeactivateAnswersBySubjectId() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deactivateAnswersBySubjectId')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->deactivateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::activateAnswersBySubjectId
  */
  public function testActivateAnswersBySubjectId() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('activateAnswersBySubjectId')
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue(
      $answerObject->activateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::addMetaForAnswerSet
  */
  public function testAddMetaForAnswerSet() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('addMetaForAnswerSet')
      ->with($this->equalTo(1), $this->equalTo('key'), $this->equalTo('value'))
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertTrue($answerObject->addMetaForAnswerSet(1, 'key', 'value'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getMetaForAnswerSet
  */
  public function testGetMetaForAnswerSet() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getMetaForAnswerSet')
      ->with($this->equalTo(1), $this->equalTo('key'))
      ->will($this->returnValue('value'));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals('value', $answerObject->getMetaForAnswerSet(1, 'key'));
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getAnswerSetsInCurrentYear
  */
  public function testGetAnswerSetsInCurrentYear() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();

    $expected = array('12345', '67890');

    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswerSetsInCurrentYear')
      ->with($this->equalTo('1234abcd'))
      ->will($this->returnValue(array('12345', '67890')));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      $expected,
      $answerObject->getAnswerSetsInCurrentYear('1234abcd')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::getAnswerSetsForSurfers
  */
  public function testGetAnswerSetsForSurfers() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('getAnswerSetsForSurfers')
      ->with($this->equalTo(array('User_1', 'User_2')))
      ->will($this->returnValue(array('Answer_1', 'Answer_2')));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);
    $this->assertEquals(
      array('Answer_1', 'Answer_2'),
      $answerObject->getAnswerSetsForSurfers(array('User_1', 'User_2'))
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::transferAnswerSetsToSurfer
  */
  public function testTransferAnswerSetsToSurfer() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('transferAnswerSetsToSurfer')
      ->with($this->equalTo(array(1234, 5678)), $this->equalTo('Surfer_1'))
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);

    $this->assertTrue(
      $answerObject->transferAnswerSetsToSurfer(
        array(1234, 5678),
        'Surfer_1'
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswer::deleteDeprecatedAnswerSets
  */
  public function testDeleteDeprecatedAnswerSets() {
    $answerObject = $this->loadQuestionnaireAnswerObjectFixture();
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseAccessObject
      ->expects($this->once())
      ->method('deleteDeprecatedAnswerSets')
      ->with($this->equalTo(array(1234, 5678)), $this->equalTo('Surfer_1'))
      ->will($this->returnValue(TRUE));
    $answerObject->setDatabaseAccessObject($databaseAccessObject);

    $this->assertTrue(
      $answerObject->deleteDeprecatedAnswerSets(
        array(1234, 5678),
        'Surfer_1'
      )
    );
  }
}