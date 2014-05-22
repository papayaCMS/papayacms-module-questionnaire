<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

require_once(dirname(__FILE__).'/../../../src/Answer/Database/Access.php');

class PapayaQuestionnaireAnswerDatabaseAccessTest extends PapayaTestCase {

  /**
  * Load PapayaQuestionnaireAnswerDatabaseAccess object fixture
  *
  * @param boolean $useProxy optional, default FALSE
  * @return PapayaQuestionnaireAnswerDatabaseAccessProxy
  */
  private function loadDatabaseAccessObjectFixture($useProxy = FALSE) {
    if ($useProxy) {
      $databaseAccessObject = new PapayaQuestionnaireAnswerDatabaseAccessProxy();
    } else {
      $databaseAccessObject = new PapayaQuestionnaireAnswerDatabaseAccess();
    }
    $configuration = $this->getMockConfigurationObject(array('PAPAYA_DB_TABLEPREFIX' => 'papaya'));
    $databaseAccessObject->setConfiguration($configuration);
    return $databaseAccessObject;
  }

  /**
  * Load db_simple mock object fixture
  *
  * @param array $methods optional, default empty array
  * @return db_simple mock object
  */
  private function loadDatabaseObjectFixture($methods) {
    $databaseObject = $this->getMock(
      'PapayaDatabaseAccess',
      $methods,
      array(),
      'Mock_'.md5(uniqid()),
      FALSE
    );
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
      'Mock_'.md5(__CLASS__.microtime()),
      FALSE
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::setConfiguration
  */
  public function testSetConfiguration() {
    $databaseAccessObject = new PapayaQuestionnaireAnswerDatabaseAccess();
    $mockConfiguration = $this->getMockConfigurationObject();
    $databaseAccessObject->setConfiguration($mockConfiguration);
    $this->assertAttributeEquals($mockConfiguration, '_configuration', $databaseAccessObject);
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getConfiguration
  */
  public function testGetConfiguration() {
    $databaseAccessObject = new PapayaQuestionnaireAnswerDatabaseAccess;
    $mockConfiguration = $this->getMockConfigurationObject();
    $mockApplication = $this->getMockApplicationObject(array('options' => $mockConfiguration));
    $databaseAccessObject->papaya($mockApplication);
    $this->assertEquals($mockConfiguration, $databaseAccessObject->getConfiguration());
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::setTableNames
  */
  public function testSetTableNames() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $this->assertEquals(
      'papaya_questionnaire_answer',
      $this->readAttribute($databaseAccessObject, '_tableAnswer')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getActiveAnswersByUserAndSubject
  */
  public function testGetActiveAnswersByUserAndSubject() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
        'getSQLCondition',
        'escapeStr',
        'masterOnly',
        'queryFmt'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_value' => 3,
                'answer_choice_id' => 1,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_value' => 1,
                'answer_choice_id' => 2,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_value' => 4,
                'answer_choice_id' => 3,
                'answer_deactivated' => 0
              )
            )
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('getSQLCondition')
      ->will($this->returnValue('question_id IN (1, 2, 3)'));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $answers = array(1 => 1, 2 => 2, 3 => 3);
    $this->assertEquals(
      $answers,
      $databaseAccessObject->getActiveAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswers
   */
  public function testGetAnswers() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
        'getSQLCondition',
        'escapeString',
        'masterOnly',
        'queryFmt'
      )
    );
    $answers = array(
      1 => array(
        'answer_set_id' => 'answer_set_id_1',
        'answer_timestamp' => 0,
        'answer_deactivated' => 0,
        'answer_choice_id' => 1,
        'answer_choice_text' => 'Answer Choice #1',
        'answer_choice_value' => 'positive',
        'question_id' => 1,
        'subject_id' => 42,
        'user_id' => 'user-id-1',
      ),
      2 => array(
        'answer_set_id' => 'answer_set_id_1',
        'answer_timestamp' => 0,
        'answer_deactivated' => 0,
        'answer_choice_id' => 2,
        'answer_choice_text' => 'Answer Choice #2',
        'answer_choice_value' => 'positive',
        'question_id' => 2,
        'subject_id' => 42,
        'user_id' => 'user-id-1',
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($answers[1]),
            $this->returnValue($answers[2])
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      $answers,
      $databaseAccessObject->getAnswers(array('answer_set_id' => 'answer_set_id_1'))
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswerOptions
   */
  public function testGetAnswerOptions() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
        'getSQLCondition',
        'escapeString',
        'masterOnly',
        'queryFmt'
      )
    );
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
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($options[11]),
            $this->returnValue($options[12])
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      $options,
      $databaseAccessObject->getAnswerOptions(array('question_id' => '1'))
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswersByUserAndSubject
  */
  public function testGetAnswersByUserAndSubject() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
      'getSQLCondition',
      'escapeStr',
      'masterOnly',
      'queryFmt'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_choice_id' => 1,
                'answer_choice_value' => 3,
                'answer_deactivated' => 0,
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_choice_id' => 2,
                'answer_choice_value' => 1,
                'answer_deactivated' => 0,
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user-id',
                'subject_id' => 'subject-id',
                'answer_choice_id' => 3,
                'answer_choice_value' => 4,
                'answer_deactivated' => 1234567890,
              )
            )
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('getSQLCondition')
      ->will($this->returnValue('question_id IN (1, 2, 3)'));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $answers = array(
      1 => array('answer_choice_id' => 1, 'answer_value' => 3, 'answer_deactivated' => 0),
      2 => array('answer_choice_id' => 2, 'answer_value' => 1,  'answer_deactivated' => 0),
      3 => array('answer_choice_id' => 3, 'answer_value' => 4,  'answer_deactivated' => 1234567890)
    );
    $this->assertEquals(
      $answers,
      $databaseAccessObject->getAnswersByUserAndSubject('user-id', 'subject-id', array(1, 2, 3))
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getActiveAnswersBySubjectId
  */
  public function testGetActiveAnswersBySubjectIdGroupedByQuestionId() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
      'escapeStr',
      'masterOnly',
      'queryFmt'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 3,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 1,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 3,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            )
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $answers = array(
      1 => array('user_id_1' => 3, 'user_id_2' => 4),
      2 => array('user_id_1' => 1, 'user_id_2' => 3),
      3 => array('user_id_1' => 4, 'user_id_2' => 4)
    );
    $this->assertEquals($answers, $databaseAccessObject->getActiveAnswersBySubjectId('subject-id', FALSE));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getActiveAnswersBySubjectId
  */
  public function testGetActiveAnswersBySubjectIdGroupedByUserId() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
      'escapeStr',
      'masterOnly',
      'queryFmt'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->any())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 3,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 1,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user_id_1',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 1,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 2,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 3,
                'answer_deactivated' => 0
              )
            ),
            $this->returnValue(
              array(
                'question_id' => 3,
                'user_id' => 'user_id_2',
                'subject_id' => 'subject-id',
                'answer_choice_value' => 4,
                'answer_deactivated' => 0
              )
            )
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $answers = array(
      'user_id_1' => array(1 => 3, 2 => 1, 3 => 4),
      'user_id_2' => array(1 => 4, 2 => 3, 3 => 4)
    );
    $this->assertEquals(
      $answers, $databaseAccessObject->getActiveAnswersBySubjectId('subject-id', TRUE)
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getActiveSubjectIdsByUser
  */
  public function testGetActiveSubjectIdsByUser() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('escapeStr', 'masterOnly', 'queryFmt'));
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $rows = array(
      array('subject_id' => 'subject_id_6', 'answer_timestamp' => 1234567892),
      array('subject_id' => 'subject_id_7', 'answer_timestamp' => 1234567891),
      array('subject_id' => 'subject_id_42', 'answer_timestamp' => 1234567890)
    );
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($rows[0]),
            $this->returnValue($rows[1]),
            $this->returnValue($rows[2]),
            FALSE
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      array(
        $rows[0]['subject_id'] => $rows[0]['answer_timestamp'],
        $rows[1]['subject_id'] => $rows[1]['answer_timestamp'],
        $rows[2]['subject_id'] => $rows[2]['answer_timestamp']
      ),
      $databaseAccessObject->getActiveSubjectIdsByUser('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getSubjectIdsByUser
  */
  public function testGetSubjectIdsByUser() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('escapeStr', 'masterOnly', 'queryFmt', 'escapeString'));
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $rows = array(
      array('subject_id' => 'subject_id_6', 'answer_timestamp' => 1234567892, 'answer_deactivated' => 0),
      array('subject_id' => 'subject_id_7', 'answer_timestamp' => 1234567891, 'answer_deactivated' => 0),
    );
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($rows[0]),
            $this->returnValue($rows[1]),
            FALSE
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseObject
      ->expects($this->atLeastOnce())
      ->method('escapeString')
      ->will($this->returnArgument(0));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      array(
        $rows[0]['subject_id'] => array(
          'subject_id' => $rows[0]['subject_id'],
          'answer_timestamp' => $rows[0]['answer_timestamp'],
          'answer_deactivated' => $rows[0]['answer_deactivated']
        ),
        $rows[1]['subject_id'] => array(
          'subject_id' => $rows[1]['subject_id'],
          'answer_timestamp' => $rows[1]['answer_timestamp'],
          'answer_deactivated' => $rows[1]['answer_deactivated']
        )
      ),
      $databaseAccessObject->getSubjectIdsByUser('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswerSets
  */
  public function testGetAnswerSets() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('escapeStr', 'masterOnly', 'queryFmt', 'escapeString', 'getSqlCondition'));
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $rows = array(
      array('answer_set_id' => 'answer_set_id_6', 'subject_id' => '4711', 'answer_timestamp' => 1234567892, 'answer_deactivated' => 0),
      array('answer_set_id' => 'answer_set_id_7', 'subject_id' => '42', 'answer_timestamp' => 1234567891, 'answer_deactivated' => 1),
    );
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($rows[0]),
            $this->returnValue($rows[1]),
            FALSE
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseObject
      ->expects($this->exactly(3))
      ->method('getSqlCondition')
      ->will($this->returnValue('a = b'));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      array(
        $rows[0]['answer_set_id'] => array(
          'answer_set_id' => $rows[0]['answer_set_id'],
          'subject_id' => $rows[0]['subject_id'],
          'answer_timestamp' => $rows[0]['answer_timestamp'],
          'answer_deactivated' => $rows[0]['answer_deactivated']
        ),
        $rows[1]['answer_set_id'] => array(
          'answer_set_id' => $rows[1]['answer_set_id'],
          'subject_id' => $rows[1]['subject_id'],
          'answer_timestamp' => $rows[1]['answer_timestamp'],
          'answer_deactivated' => $rows[1]['answer_deactivated']
        )
      ),
      $databaseAccessObject->getAnswerSets(array('user_id' => 'user-id', 'pool_id' => 1), 'DESC')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::markAnswerSetDeleted
  */
  public function testMarkAnswersetDeleted() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->markAnswerSetDeleted('answerset-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getActiveSubjectIdsByMetaKeyValue
  */
  public function testGetActiveSubjectIdsByMetaKeyValue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('escapeStr', 'masterOnly', 'queryFmt', 'getSqlCondition'));
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $rows = array(
      array('subject_id' => 'subject_id_6', 'answer_timestamp' => 1234567892),
      array('subject_id' => 'subject_id_7', 'answer_timestamp' => 1234567891),
      array('subject_id' => 'subject_id_42', 'answer_timestamp' => 1234567890)
    );
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue($rows[0]),
            $this->returnValue($rows[1]),
            $this->returnValue($rows[2]),
            FALSE
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseObject
      ->expects($this->exactly(2))
      ->method('getSqlCondition')
      ->will($this->returnValue("test='tast"));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      array(
        $rows[0]['subject_id'] => $rows[0]['answer_timestamp'],
        $rows[1]['subject_id'] => $rows[1]['answer_timestamp'],
        $rows[2]['subject_id'] => $rows[2]['answer_timestamp']
      ),
      $databaseAccessObject->getActiveSubjectIdsByMetaKeyValue('user-id', 'keyname', 'keyvalue')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswerTimestampByUserAndSubject
  */
  public function testGetAnswerTimestampByUserAndSubject() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('escapeStr', 'masterOnly', 'queryFmt'));
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $databaseResultObject
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(1234567890));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(
      1234567890,
      $databaseAccessObject->getAnswerTimestampByUserAndSubject('user-id', 'subject-id')
    );
  }


  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::_createAnswerSet
  */
  public function testCreateAnswerSet() {
    $databaseAccessObject = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_createAnswerSet'));
    $databaseObject = $this->loadDatabaseObjectFixture(array('insertRecord'));
    $data = array(
      'user_id' => 'my-user',
      'subject_id' => 'my-subject',
      'answer_timestamp' => 123456789,
      'answer_deactivated' => 0,
      'answer_deleted' => 0,
    );
    $databaseObject
      ->expects($this->once())
      ->method('insertRecord')
      ->with(
        $this->equalTo($this->readAttribute($databaseAccessObject, '_tableAnswerSet')),
        $this->equalTo('answer_set_id'),
        $this->equalTo($data)
      )
      ->will($this->returnValue(123));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertEquals(123, $databaseAccessObject->_createAnswerSet('my-user', 'my-subject', 123456789));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::saveAnswersOfUserForSubject
  */
  public function testSaveAnswersOfUserForSubject() {
    $userId = 'user-id';
    $subjectId = 'subject-id';
    $answers = array(132);
    $mockDBA = $this->getMock('PapayaQuestionnaireAnswerDatabaseAccess', array('_createAnswerSet'));
    $mockDBA
      ->expects($this->once())
      ->method('_createAnswerSet')
      ->with($this->equalTo($userId), $this->equalTo($subjectId), $this->anything())
      ->will($this->returnValue(12));

    $databaseObject = $this->loadDatabaseObjectFixture(array('insertRecords'));
    $data = array(
      0 => array(
        'answer_set_id' => 12,
        'answer_choice_id' => 132,
      )
    );
    $databaseObject
      ->expects($this->once())
      ->method('insertRecords')
      ->with(
        $this->equalTo($this->readAttribute($mockDBA, '_tableAnswer')),
        $this->equalTo($data)
      )
      ->will($this->returnValue(123));
    $mockDBA->setDatabaseAccess($databaseObject);
    $mockDBA->saveAnswersOfUserForSubject($userId, $subjectId, $answers);
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::_deleteAnswerSetByUserAndSubject
  */
  public function testDeleteAnswerSetByUserAndSubject() {
    $userId = 'user-id';
    $subjectId = 'subject-id';
    $proxyDBA = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_deleteAnswerSetByUserAndSubject'));
    $mockDBA = $this->getMock(get_class($proxyDBA), array('_getAnswerSetId', 'markAnswerSetDeleted'));
    $mockDBA
      ->expects($this->once())
      ->method('_getAnswerSetId')
      ->with($this->equalTo($userId), $this->equalTo($subjectId))
      ->will($this->returnValue(132));
    $mockDBA
      ->expects($this->once())
      ->method('markAnswerSetDeleted')
      ->with($this->equalTo(132))
      ->will($this->returnValue(TRUE));
    $this->assertTrue($mockDBA->_deleteAnswerSetByUserAndSubject($userId, $subjectId));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::_deleteAnswerSet
  */
  public function testDeleteAnswerSet() {
    $proxyDBA = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_deleteAnswerSet'));
    $mockDBA = $this->getMock(get_class($proxyDBA), array('markAnswerSetDeleted'));
    $mockDBA
      ->expects($this->once())
      ->method('markAnswerSetDeleted')
      ->with($this->equalTo(155))
      ->will($this->returnValue(TRUE));
    $this->assertTrue($mockDBA->_deleteAnswerSet(155));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::_getAnswerSetId
  */
  public function testGetAnswerSetId() {
    $proxyDBA = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_getAnswerSetId'));
    $dbResultMock = $this->loadDatabaseResultObjectFixture(array('fetchField'));
    $dbResultMock
      ->expects($this->once())
      ->method('fetchField')
      ->will($this->returnValue(123));
    $databaseObject = $this->loadDatabaseObjectFixture(array('queryFmt'));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($dbResultMock));
    $proxyDBA->setDatabaseAccess($databaseObject);
    $this->assertEquals(123, $proxyDBA->_getAnswerSetId('user-id', 'subject-id'));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::saveAnswersByUserAndSubject
  */
  public function testSaveAnswersByUserAndSubjectExpectingTrue() {
    $dbAccessProxy = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_createAnswerSet'));
    $dbAccessMock = $this->getMock(
      get_class($dbAccessProxy),
      array('_createAnswerSet', 'deleteAnswersByUserAndSubject')
    );
    $dbAccessMock
      ->expects($this->once())
      ->method('_createAnswerSet')
      ->with($this->equalTo('user-id'), $this->equalTo('subject-id'))
      ->will($this->returnValue(7));
    $dbAccessMock
      ->expects($this->once())
      ->method('deleteAnswersByUserAndSubject')
      ->will($this->returnValue(TRUE));

    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
      'getSQLCondition',
      'escapeStr',
      'masterOnly',
      'queryFmt',
      'setDataModified',
      'insertRecord',
      'deleteRecord'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(array('question_id' => 1, 'question_identifier' => 'q1')),
            $this->returnValue(array('question_id' => 2, 'question_identifier' => 'q2')),
            $this->returnValue(array('question_id' => 3, 'question_identifier' => 'q3'))
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('getSQLCondition')
      ->will($this->returnValue('question_id IN (1, 2, 3)'));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseObject
      ->expects($this->atLeastOnce())
      ->method('insertRecord')
      ->will($this->returnValue(TRUE));
    $dbAccessMock->setDatabaseAccess($databaseObject);
    $this->assertEquals(7,
      $dbAccessMock->saveAnswersByUserAndSubject(
        'user-id', 'subject-id', array(1 => 3, 2 => 1, 3 => 4)
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::saveAnswersByUserAndSubject
  */
  public function testSaveAnswersByUserAndSubjectExpectingFalse() {
    $dbAccessProxy = $this->getProxy('PapayaQuestionnaireAnswerDatabaseAccess', array('_createAnswerSet'));
    $dbAccessMock = $this->getMock(
      get_class($dbAccessProxy),
      array('_createAnswerSet', 'deleteAnswersByUserAndSubject')
    );
    $dbAccessMock
      ->expects($this->once())
      ->method('_createAnswerSet')
      ->with($this->equalTo('user-id'), $this->equalTo('subject-id'))
      ->will($this->returnValue(7));
    $dbAccessMock
      ->expects($this->once())
      ->method('deleteAnswersByUserAndSubject')
      ->will($this->returnValue(TRUE));

    $databaseObject = $this->loadDatabaseObjectFixture(
      array(
        'getSQLCondition',
        'escapeStr',
        'masterOnly',
        'queryFmt',
        'setDataModified',
        'insertRecord',
        'deleteRecord'
      )
    );
    $databaseResultObject = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $databaseResultObject
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will(
          $this->onConsecutiveCalls(
            $this->returnValue(array('question_id' => 1, 'question_identifier' => 'q1')),
            $this->returnValue(array('question_id' => 2, 'question_identifier' => 'q2')),
            $this->returnValue(array('question_id' => 3, 'question_identifier' => 'q3'))
          )
        );
    $databaseObject
      ->expects($this->once())
      ->method('getSQLCondition')
      ->will($this->returnValue('question_id IN (1, 2, 3)'));
    $databaseObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($databaseResultObject));
    $databaseObject
      ->expects($this->atLeastOnce())
      ->method('insertRecord')
      ->will($this->returnValue(FALSE));
    $dbAccessMock->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $dbAccessMock->saveAnswersByUserAndSubject(
        'user-id', 'subject-id', array(1 => 3, 2 => 1, 3 => 4)
      )
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deleteAnswersByUserAndSubject
  */
  public function testDeleteAnswersByUserAndSubjectExpectingTrue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture(TRUE);
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'deleteRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('deleteRecord')
      ->will($this->returnValue(3));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->deleteAnswersByUserAndSubject('user-id', 'subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deleteAnswersByUserAndSubject
  */
  public function testDeleteAnswersByUserAndSubjectExpectingFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture(TRUE);
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'deleteRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('deleteRecord')
      ->will($this->returnValue(FALSE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $databaseAccessObject->deleteAnswersByUserAndSubject('user-id', 'subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deactivateAnswersByUserId
  */
  public function testDeactivateAnswersByUserIdExpectingTrue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->deactivateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deactivateAnswersByUserId
  */
  public function testDeactivateAnswersByUserIdExpectingFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(FALSE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $databaseAccessObject->deactivateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::activateAnswersByUserId
  */
  public function testActivateAnswersByUserIdExpectingTrue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->activateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::activateAnswersByUserId
  */
  public function testActivateAnswersByUserIdExpectingFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(FALSE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $databaseAccessObject->activateAnswersByUserId('user-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deactivateAnswersBySubjectId
  */
  public function testDeactivateAnswersBySubjectIdExpectingTrue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->deactivateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::deactivateAnswersBySubjectId
  */
  public function testDeactivateAnswersBySubjectIdExpectingFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(FALSE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $databaseAccessObject->deactivateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::activateAnswersBySubjectId
  */
  public function testActivateAnswersBySubjectIdExpectingTrue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->activateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::activateAnswersBySubjectId
  */
  public function testActivateAnswersBySubjectIdExpectingFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(FALSE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertFalse(
      $databaseAccessObject->activateAnswersBySubjectId('subject-id')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::addMetaForAnswerSet
  */
  public function testAddMetaForAnswerSet() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $data = array(
      'answer_set_id' => 123,
      'answer_set_meta_key' => 'blah',
      'answer_set_meta_value' => 'blupp',
    );
    $mockDBObject = $this->loadDatabaseObjectFixture(array('insertRecord'));
    $mockDBObject
      ->expects($this->once())
      ->method('insertRecord')
      ->with(
        $this->equalTo($this->readAttribute($databaseAccessObject, '_tableAnswerSetMeta')),
        $this->equalTo('answer_set_meta_id'),
        $this->equalTo($data)
      )
      ->will($this->returnValue(132));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);
    $this->assertEquals(132, $databaseAccessObject->addMetaForAnswerSet(123, 'blah', 'blupp'));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getMetaForAnswerSet
  */
  public function testGetMetaForAnswerSet() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();
    $expected = array(
      17 => array(
        'answer_set_meta_id' => 17,
        'answer_set_id' => 132,
        'answer_set_meta_key' => 'blah',
        'answer_set_meta_value' => 'blupp',
      ),
    );
    $mockDBResult = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockDBResult
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls(
        $this->returnValue($expected[17]),
        $this->returnValue(FALSE)
      ));

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmt', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDBResult));
    $mockDBObject
      ->expects($this->once())
      ->method('getSqlCondition')
      ->will($this->returnValue("test='tast"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);
    $this->assertEquals($expected, $databaseAccessObject->getMetaForAnswerSet(132, 'blah'));
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswerSetsInCurrentYear
  */
  public function testGetAnswerSetsInCurrentYear() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture(TRUE);

    $expected = array('2713', '83762', '3525');

    $mockDBResult = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockDBResult
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls(
        $this->returnValue(array('answer_set_id' => '2713')),
        $this->returnValue(array('answer_set_id' => '83762')),
        $this->returnValue(array('answer_set_id' => '3525')),
        $this->returnValue(FALSE)
      ));

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmt', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDBResult));
    $mockDBObject
      ->expects($this->once())
      ->method('getSqlCondition')
      ->with($this->equalTo('qas.user_id'), $this->equalTo('12345abcd'))
      ->will($this->returnValue("qas.user_id='12345abcd'"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertEquals(
      $expected,
      $databaseAccessObject->getAnswerSetsInCurrentYear('12345abcd')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::GetYearRangeCondition
  */
  public function testGetYearRangeConditionWithNegativeValue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $expected = "BETWEEN
            UNIX_TIMESTAMP(
              CONCAT(
                DATE_FORMAT(NOW(),'%%Y'),
                DATE_FORMAT(
                  FROM_UNIXTIME(ps.surfer_registration),'-%%m-%%d %%H:%%i:%%s'
                )
              )
            )
            AND
            UNIX_TIMESTAMP(
              CONCAT(
                DATE_FORMAT(NOW(),'%%Y')+1,
                DATE_FORMAT(
                  FROM_UNIXTIME(ps.surfer_registration),'-%%m-%%d %%H:%%i:%%s'
                )
              )
            )";

    $dbResultValue = array('difference' => -12345);

    $mockDBResult = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockDBResult
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls(
        $this->returnValue($dbResultValue),
        $this->returnValue(FALSE)
      ));

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmt', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDBResult));
    $mockDBObject
      ->expects($this->once())
      ->method('getSqlCondition')
      ->with($this->equalTo('surfer_id'), $this->equalTo('12345abcd'))
      ->will($this->returnValue("surfer_id='12345abcd'"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertEquals(
      $expected,
      $databaseAccessObject->getYearRangeCondition('12345abcd')
    );
  }

  /**
  * @covers PapayaQuestionnaireAnswerDatabaseAccess::GetYearRangeCondition
  */
  public function testGetYearRangeConditionWithPositiveValue() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $expected = "BETWEEN
            UNIX_TIMESTAMP(
              CONCAT(
                DATE_FORMAT(NOW(),'%%Y')-1,
                DATE_FORMAT(
                  FROM_UNIXTIME(ps.surfer_registration),'-%%m-%%d %%H:%%i:%%s'
                )
              )
            )
            AND
            UNIX_TIMESTAMP(
              CONCAT(
                DATE_FORMAT(NOW(),'%%Y'),
                DATE_FORMAT(
                  FROM_UNIXTIME(ps.surfer_registration),'-%%m-%%d %%H:%%i:%%s'
                )
              )
            )";

    $dbResultValue = array('difference' => 12345);

    $mockDBResult = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockDBResult
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls(
        $this->returnValue($dbResultValue),
        $this->returnValue(FALSE)
      ));

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmt', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDBResult));
    $mockDBObject
      ->expects($this->once())
      ->method('getSqlCondition')
      ->with($this->equalTo('surfer_id'), $this->equalTo('12345abcd'))
      ->will($this->returnValue("surfer_id='12345abcd'"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertEquals(
      $expected,
      $databaseAccessObject->getYearRangeCondition('12345abcd')
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::getAnswerSetsForSurfers
   */
  public function testGetAnswerSetsForSurfers() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $dbResultValue = array(
      array(
        'answer_set_id' => 1234,
        'answer_timestamp' => 1234567890,
        'user_id' => 'UserId_1',
        'subject_id' => 'SubjectId_1',
        'answer_deleted' => 0,
        'answer_deactivated' => 0,
      ),
      array(
        'answer_set_id' => 5678,
        'answer_timestamp' => 2234567890,
        'user_id' => 'UserId_2',
        'subject_id' => 'SubjectId_2',
        'answer_deleted' => 0,
        'answer_deactivated' => 0,
      )
    );

    $mockDBResult = $this->loadDatabaseResultObjectFixture(array('fetchRow'));
    $mockDBResult
      ->expects($this->atLeastOnce())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls(
        $this->returnValue($dbResultValue[0]),
        $this->returnValue($dbResultValue[1]),
        $this->returnValue(FALSE)
      ));

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmt', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDBResult));
    $mockDBObject
      ->expects($this->once())
      ->method('getSqlCondition')
      ->with($this->equalTo('user_id'), $this->equalTo(array('UserId_1', 'UserId_2')))
      ->will($this->returnValue("user_id IN ('UserId_1', 'UserId_2'"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertEquals(
      $dbResultValue,
      $databaseAccessObject->getAnswerSetsForSurfers(array('UserId_1', 'UserId_2'))
    );

  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::transferAnswerSetsToSurfer
   */
  public function testTransferAnswerSetsToSurfer() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $databaseObject = $this->loadDatabaseObjectFixture(array('setDataModified', 'updateRecord'));
    $databaseObject
      ->expects($this->once())
      ->method('updateRecord')
      ->will($this->returnValue(TRUE));
    $databaseAccessObject->setDatabaseAccess($databaseObject);
    $this->assertTrue(
      $databaseAccessObject->transferAnswerSetsToSurfer(array(1234,5678), 'UserId')
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::deleteDeprecatedAnswerSets
   */
  public function testDeleteDeprecatedAnswerSets() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmtWrite', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmtWrite')
      ->will($this->returnValue(2));
    $mockDBObject
      ->expects($this->at(0))
      ->method('getSqlCondition')
      ->with($this->equalTo('user_id'), $this->equalTo('UserId_1'))
      ->will($this->returnValue("user_id = 'UserId_1'"));
    $mockDBObject
      ->expects($this->at(1))
      ->method('getSqlCondition')
      ->with($this->equalTo('answer_set_id'), $this->equalTo(array(1234,5678)))
      ->will($this->returnValue("answer_set_id IN ('1234', '5679')"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertTrue(
      $databaseAccessObject->deleteDeprecatedAnswerSets(
        array(1234, 5678),
        'UserId_1'
      )
    );
  }

  /**
   * @covers PapayaQuestionnaireAnswerDatabaseAccess::deleteDeprecatedAnswerSets
   */
  public function testDeleteDeprecatedAnswerSetsReturnsFalse() {
    $databaseAccessObject = $this->loadDatabaseAccessObjectFixture();

    $mockDBObject = $this->loadDatabaseObjectFixture(array('queryFmtWrite', 'getSqlCondition'));
    $mockDBObject
      ->expects($this->once())
      ->method('queryFmtWrite')
      ->will($this->returnValue(FALSE));
    $mockDBObject
      ->expects($this->at(0))
      ->method('getSqlCondition')
      ->with($this->equalTo('user_id'), $this->equalTo('UserId_1'))
      ->will($this->returnValue("user_id = 'UserId_1'"));
    $mockDBObject
      ->expects($this->at(1))
      ->method('getSqlCondition')
      ->with($this->equalTo('answer_set_id'), $this->equalTo(array(1234,5678)))
      ->will($this->returnValue("answer_set_id IN ('1234', '5679')"));
    $databaseAccessObject->setDatabaseAccess($mockDBObject);

    $this->assertFalse(
      $databaseAccessObject->deleteDeprecatedAnswerSets(
        array(1234, 5678),
        'UserId_1'
      )
    );
  }
}

class PapayaQuestionnaireAnswerDatabaseAccessProxy extends PapayaQuestionnaireAnswerDatabaseAccess {
  public function deleteAnswersByUserAndSubject($userId, $subjectId) {
    return parent::deleteAnswersByUserAndSubject($userId, $subjectId);
  }

  public function getYearRangeCondition($surferId) {
    return 'awesomeCondition';
  }
}