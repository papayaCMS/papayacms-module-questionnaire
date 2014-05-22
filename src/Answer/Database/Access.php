<?php
/**
* The Answer database access class handles the database operations for storage and
* retrieval of questionnaire answers.
*
* @copyright 2002-2010 by papaya Software GmbH - All rights reserved.
* @link http://www.papaya-cms.com/
* @license papaya Commercial License (PCL)
*
* Redistribution of this script or derivated works is strongly prohibited!
* The Software is protected by copyright and other intellectual property
* laws and treaties. papaya owns the title, copyright, and other intellectual
* property rights in the Software. The Software is licensed, not sold.
*
* @package Commercial
* @subpackage Questionnaire
* @version $Id: Access.php 2 2013-12-09 16:39:31Z weinert $
* @tutorial commercial/Questionnaire/PapayaQuestionnaireAnswerDatabaseAccess.cls
*/

/**
* Base class papaya database access
*/
require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_db.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireAnswerDatabaseAccess extends base_db {
  /**
  * Configuration object
  * @var PapayaConfiguration
  */
  protected $_configuration = NULL;

  /**
  * Database table answers
  * @var string
  */
  protected $_tableAnswer = '';

  protected $_tableAnswerSet;

  protected $_tableAnswerSetMeta;

  protected $_tableAnswerOptions;

  protected $_tableSurfer;

  /**
  * Database table questions
  * @var string
  */
  protected $_tableQuestion = '';

  /**
  * Set configuration object
  *
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    $this->_configuration = $configuration;
    $this->setTableNames();
  }

  public function getConfiguration() {
    if (empty($this->_configuration)) {
      $this->setConfiguration($this->papaya()->options);
    }
    return $this->_configuration;
  }

  /**
  * Set database table names
  */
  public function setTableNames() {
    $tablePrefix = $this->getConfiguration()->getOption('PAPAYA_DB_TABLEPREFIX');
    $this->_tableAnswer = $tablePrefix.'_questionnaire_answer';
    $this->_tableAnswerSet = $tablePrefix.'_questionnaire_answer_set';
    $this->_tableAnswerSetMeta = $tablePrefix.'_questionnaire_answer_set_meta';
    $this->_tableAnswerOptions = $tablePrefix.'_questionnaire_answer_options';
    $this->_tableQuestion = $tablePrefix.'_questionnaire_question';
    $this->_tableSurfer = $tablePrefix.'_surfer';
  }

  /**
  * Get answers without deactivation timestamp by user id and subject id
  *
  * If you provide an array of question ids, only the answers for these questions are loaded.
  * Otherwise, all answers for the user are loaded.
  * The result has got the following format:
  * array($questionId => $answer, ...)
  * If no answers are available, an empty array is returned.
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $questionIds optional, default empty array
  * @return array
  */
  public function getActiveAnswersByUserAndSubject($userId, $subjectId, $questionIds = array()) {
    $result = FALSE;
    $questionCondition = '1=1';
    if (!empty($questionIds)) {
      $questionCondition = $this->databaseGetSqlCondition('question_id', $questionIds);
    }
    $sql = "SELECT ac.question_id, answer_choice_value, answer_choice_id
              FROM %s AS aset
             RIGHT JOIN %s AS a USING (answer_set_id)
             RIGHT JOIN %s AS ac USING (answer_choice_id)
             WHERE aset.user_id = '%s' AND aset.subject_id = '%s'
               AND $questionCondition
               AND aset.answer_deactivated = 0
               AND aset.answer_deleted = 0
           ";
    $params = array(
      $this->_tableAnswerSet,
      $this->_tableAnswer,
      $this->_tableAnswerOptions,
      $userId,
      $subjectId,
    );
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_id']] = $row['answer_choice_id'];
      }
    }
    return $result;
  }

  /**
   * Get answers matching a filter
   * @param array $filter array of database field => value(s)
   * @return array matching answers
   */
  public function getAnswers($filter = array()) {
    // fields in the tables
    $fields = array(
      'answer_set_id' => 'answer_set.answer_set_id',
      'answer_timestamp' => 'answer_set.answer_timestamp',
      'answer_deactivated' => 'answer_set.answer_deactivated',
      'answer_deleted' => 'answer_set.answer_deleted',
      'answer_choice_id' => 'answer_options.answer_choice_id',
      'answer_choice_text' => 'answer_options.answer_choice_text',
      'answer_choice_value' => 'answer_options.answer_choice_value',
      'question_id' => 'answer_options.question_id',
      'subject_id' => 'answer_set.subject_id',
      'user_id' => 'answer_set.user_id',
    );
    // build select fields
    $selectFields = array();
    foreach ($fields as $alias => $field) {
      $selectFields[] = $field . ' AS ' . $alias;
    }
    $sqlFields = implode(', ', $selectFields);
    // build WHERE condition
    $filters = array();
    foreach ($filter as $field => $value) {
      if (isset($fields[$field])) {
        $filters[] = $this->databaseGetSqlCondition($fields[$field], $value);
      }
    }
    if (count($filters) > 0) {
      $sqlWhere = 'WHERE ' . implode(' AND ', $filters);
    }
    // build SQL query
    $sql = "SELECT $sqlFields
              FROM %s AS answer
             RIGHT JOIN %s AS answer_set USING (answer_set_id)
             RIGHT JOIN %s AS answer_options USING (answer_choice_id)
             $sqlWhere";
    $params = array(
      $this->_tableAnswer,
      $this->_tableAnswerSet,
      $this->_tableAnswerOptions,
    );
    // process result
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_id']] = $row;
      }
    }
    return $result;
  }

  /**
   * Get answer options (=possible answers for questions)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @return array answer options, key is the answer_choice_id, empty array if nothing found
   */
  public function getAnswerOptions($filter = array()) {
    // fields in the table
    $fields = array(
      'question_id',
      'answer_choice_id',
      'answer_choice_text',
      'answer_choice_value',
    );
    // build filter
    $filters = array();
    foreach ($filter as $field => $value) {
      if (in_array($field, $fields)) {
        $filters[] = $this->databaseGetSqlCondition($field, $value);
      }
    }
    // build sql query
    $sqlFields = implode(', ', $fields);
    $sqlWhere = '';
    if (count($filters) > 0) {
      $sqlWhere = 'WHERE ' . implode(' AND ', $filters);
    }
    $sql = "SELECT DISTINCT $sqlFields
              FROM %s
             $sqlWhere";
    $sqlParams = array($this->_tableAnswerOptions);
    // process result
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if (!isset($result[$row['answer_choice_id']])) {
          $result[$row['answer_choice_id']] = $row;
        }
      }
    }
    return $result;
  }

  /**
  * Get all available answers by user id and subject id
  *
  * The result has got the following format:
  * array($questionId => array('answer_value' => $value, 'answer_deactivated' => $timestamp), ...)
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $questionIds optional, default empty array
  * @return array
  */
  public function getAnswersByUserAndSubject($userId, $subjectId, $questionIds = array()) {
    $questionCondition = $this->databaseGetSQLCondition('ac.question_id', $questionIds);
    $sql = "SELECT ac.question_id, aset.user_id, aset.subject_id, ac.answer_choice_id,
                   answer_choice_value, aset.answer_deactivated, aset.answer_timestamp
              FROM %s AS a
             RIGHT JOIN %s AS aset USING (answer_set_id)
             RIGHT JOIN %s AS ac USING (answer_choice_id)
             WHERE $questionCondition
               AND aset.user_id = '%s'
               AND aset.subject_id = '%s'
               AND aset.answer_deleted = 0";
    $params = array(
      $this->_tableAnswer,
      $this->_tableAnswerSet,
      $this->_tableAnswerOptions,
      $userId,
      $subjectId
    );
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_id']] = array(
          'answer_choice_id' => $row['answer_choice_id'],
          'answer_value' => $row['answer_choice_value'],
          'answer_deactivated' => $row['answer_deactivated']
        );
      }
    }
    return $result;
  }

  /**
  * Get active answers by subject id
  *
  * By default, the result has got the following format:
  * array($questionId => array($userId => $answer, ...), ...)
  * If you set the optional $byUser parameter to TRUE, the format will switch to:
  * array($userId => array($questionId => $answer, ...), ...)
  *
  * @param string $subjectId
  * @param boolean $byUser optional, default FALSE
  * @return array
  */
  public function getActiveAnswersBySubjectId($subjectId, $byUser = FALSE) {
    $sql = "SELECT question_id, user_id, subject_id,
                   answer_choice_value, answer_deactivated, answer_timestamp
              FROM %s
             WHERE subject_id = '%s'
               AND answer_deactivated = 0
               AND answer_deleted = 0";
    $sqlParams = array($this->_tableAnswer, $subjectId);
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if ($byUser) {
          if (!isset($result[$row['user_id']])) {
            $result[$row['user_id']] = array();
          }
          $result[$row['user_id']][$row['question_id']] = $row['answer_choice_value'];
        } else {
          if (!isset($result[$row['question_id']])) {
            $result[$row['question_id']] = array();
          }
          $result[$row['question_id']][$row['user_id']] = $row['answer_choice_value'];
        }
      }
    }
    return $result;
  }

  /**
  * Get active subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc order by ascendent timestamp
  * @return array
  */
  public function getActiveSubjectIdsByUser($userId, $asc = FALSE) {
    $order = $asc === TRUE ? 'ASC' : 'DESC';
    $sql = "SELECT DISTINCT subject_id, answer_timestamp
              FROM %s
             WHERE user_id = '%s'
               AND answer_deactivated = 0
               AND answer_deleted = 0
             ORDER BY answer_timestamp $order";
    $sqlParams = array($this->_tableAnswerSet, $userId);
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if (!isset($result[$row['subject_id']])) {
          $result[$row['subject_id']] = $row['answer_timestamp'];
        }
      }
    }
    return $result;
  }

  /**
  * Get active subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps and deletion timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc order by ascendent timestamp
  * @return array
  */
  public function getSubjectIdsByUser($userId, $asc = FALSE) {
    $order = $asc === TRUE ? 'ASC' : 'DESC';
    $sql = "SELECT DISTINCT subject_id, answer_timestamp, answer_deactivated
              FROM %s
             WHERE user_id = '%s'
               AND answer_deleted = 0
             ORDER BY answer_timestamp $order";
    $sqlParams = array($this->_tableAnswerSet, $this->databaseEscapeString($userId));
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if (!isset($result[$row['subject_id']])) {
          $result[$row['subject_id']] = $row;
        }
      }
    }

    return $result;
  }

  /**
   * Get answer sets (=filled out questionnaires)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @param ASC|DESC $sort sort order
   * @return array answer sets, key is the answer_set_id, empty array if nothing found
   */
  public function getAnswerSets($filter, $sort = 'ASC') {
    // fields in the table
    $fields = array(
      'answer_set_id',
      'user_id',
      'subject_id',
      'answer_timestamp',
      'answer_deactivated',
      'answer_deleted',
    );
    // build filter
    $filters = array();
    $filters[] = $this->databaseGetSqlCondition('a.answer_deleted', 0);
    $poolFilter = '';
    foreach ($filter as $field => $value) {
      if (in_array($field, $fields)) {
        $filters[] = $this->databaseGetSqlCondition('a.'.$field, $value);
      }
      if ($field == 'pool_id') {
        $poolFilter = 'AND ' . $this->databaseGetSqlCondition('am.answer_set_meta_value', $value);
      }
    }
    // build sql query
    foreach ($fields as $key => $field) {
      $fields[$key] = 'a.'.$field;
    }
    $sqlFields = implode(', ', $fields);
    $sqlWhere = '';
    if (count($filters) > 0) {
      $sqlWhere = 'WHERE ' . implode(' AND ', $filters);
    }
    $sqlOrder = (strtoupper($sort) == 'DESC') ? 'DESC' : 'ASC';
    $sql = "SELECT DISTINCT $sqlFields, am.answer_set_meta_value AS pool_id
              FROM %s a
              LEFT JOIN %s am ON (
                        am.answer_set_meta_key = 'pool_id'
                        AND am.answer_set_id = a.answer_set_id
                        $poolFilter
                   )
             $sqlWhere
             ORDER BY a.answer_timestamp $sqlOrder";
    $sqlParams = array(
      $this->_tableAnswerSet,
      $this->_tableAnswerSetMeta
    );
    // process result
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if (!isset($result[$row['answer_set_id']])) {
          $result[$row['answer_set_id']] = $row;
        }
      }
    }
    return $result;
  }

  /**
   * Mark an answer set as deleted
   * @param string $answerSetId answer set id of the answer
   * @return boolean TRUE on success
   */
  public function markAnswerSetDeleted($answerSetId) {
    return (bool)$this->databaseUpdateRecord(
      $this->_tableAnswerSet,
      array('answer_deleted' => time()),
      'answer_set_id',
      $answerSetId
    );
  }

  /**
  * Get active subject ids by user id filtered by meta key value conditions
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param string $metaKey
  * @param string $metaValue optional
  * @return array
  */
  public function getActiveSubjectIdsByMetaKeyValue($userId, $metaKey, $metaValue = NULL) {
    $metaKeySqlCondition = $this->databaseGetSqlCondition(
      'answer_set_meta.answer_set_meta_key',
      $metaKey
    );
    $metaValueSqlCondition = '';
    if (!is_null($metaValue)) {
      $metaValueSqlCondition = 'AND '.$this->databaseGetSqlCondition(
        'answer_set_meta.answer_set_meta_key',
        $metaKey
      );
    }
    $sql = "SELECT DISTINCT answer_set.subject_id, answer_set.answer_timestamp
              FROM %s answer_set
             INNER JOIN %s answer_set_meta ON (
               answer_set.answer_set_id = answer_set_meta.answer_set_id
               AND $metaKeySqlCondition $metaValueSqlCondition
             )
             WHERE user_id = '%s'
               AND answer_deactivated = 0
               AND answer_deleted = 0
             ORDER BY answer_timestamp DESC";
    $sqlParams = array(
      $this->_tableAnswerSet,
      $this->_tableAnswerSetMeta,
      $userId,
    );
    $result = array();
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        if (!isset($result[$row['subject_id']])) {
          $result[$row['subject_id']] = $row['answer_timestamp'];
        }
      }
    }
    return $result;
  }

  /**
  * Get answer timestamp by user id and subject id
  *
  * If answers for the same subject by the same user should have different timestamps
  * (which shouldn't happen under normal circumstances), the latest timestamp is returned.
  * If there are no answers for the selected subject by the selected user,
  * the return value is 0.
  *
  * @param string $userId
  * @param string $subjectId
  * @return integer timestamp if available, 0 otherwise
  */
  public function getAnswerTimestampByUserAndSubject($userId, $subjectId) {
    $sql = "SELECT answer_timestamp
              FROM %s
             WHERE user_id = '%s'
               AND subject_id = '%s'
               AND answer_deleted = 0
           ";
    $sqlParams = array($this->_tableAnswerSet, $userId, $subjectId);
    $result = 0;
    if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  protected function _createAnswerSet($userId, $subjectId, $timestamp = NULL) {
    $result = FALSE;

    $timestamp = (is_null($timestamp)) ? time() : $timestamp;

    $data = array(
      'user_id' => $userId,
      'subject_id' => $subjectId,
      'answer_timestamp' => $timestamp,
      'answer_deactivated' => 0,
      'answer_deleted' => 0,
    );

    if ($setId = $this->databaseInsertRecord($this->_tableAnswerSet, 'answer_set_id', $data)) {
      $result = $setId;
    }

    return $result;
  }

  /**
  * Saves answers
  *
  * @param string $userId
  * @param integer $subjectId
  * @param array $answers
  * @param integer $answerSetId if defined answerset data will be deleted before save
  */
  public function saveAnswersOfUserForSubject($userId, $subjectId, $answers, $answerSetId = NULL) {
    $result = FALSE;
    if (empty($answerSetId) || $this->_deleteAnswerSet($answerSetId)) {
      $answerSetId = $this->_createAnswerSet($userId, $subjectId, time());
      $data = array();
      foreach ($answers as $answerChoiceId) {
        $data[] = array(
          'answer_set_id' => $answerSetId,
          'answer_choice_id' => $answerChoiceId,
        );
      }
      if ($this->databaseInsertRecords($this->_tableAnswer, $data)) {
        $result = $answerSetId;
      }
    }
    return $result;
  }

  protected function _deleteAnswerSetByUserAndSubject($userId, $subjectId) {
    $result = TRUE;
    if ($answerSet = $this->_getAnswerSetId($userId, $subjectId)) {
      $result = $this->markAnswerSetDeleted($answerSet);
    }
    return $result;
  }

  protected function _deleteAnswerSet($answerSetId) {
    return $this->markAnswerSetDeleted($answerSetId);
  }

  protected function _getAnswerSetId($userId, $subjectId) {
    $result = FALSE;
    $sql = "SELECT answer_set_id
              FROM %s
             WHERE user_id = '%s'
               AND subject_id = '%s'
               AND answer_deleted = 0
           ";
    $params = array(
      $this->_tableAnswerSet,
      $userId,
      $subjectId
    );
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  /**
  * Save answers by user id and subject id
  *
  * You need to provide the user and subject ids
  * as well as an array of answers in the following format:
  * array($questionId => $answer, ...)
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $answers
  * @return integer|boolean answer_set_id on success, FALSE otherwise
  */
  public function saveAnswersByUserAndSubject($userId, $subjectId, $answers) {
    // First, delete all old answers for this user and subject
    $result = $this->deleteAnswersByUserAndSubject($userId, $subjectId);
    // Only save and do everything else if this worked without an error
    if (FALSE !== $result) {
      if ($setId = $this->_createAnswerSet($userId, $subjectId)) {
        // Get question identifiers for the question ids
        $questionCondition = $this->databaseGetSQLCondition('question_id', array_keys($answers));
        $sql = "SELECT question_id, question_identifier
                FROM %s
                WHERE $questionCondition";
        $sqlParams = array($this->_tableQuestion);
        $identifiers = array();
        if ($res = $this->databaseQueryFmt($sql, $sqlParams)) {
          while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $identifiers[$row['question_id']] = $row['question_identifier'];
          }
        }
        // If nothing further fails, set the set id as the result
        $result = $setId;
        $timestamp = time();
        // Insert each answer
        foreach ($answers as $questionId => $answerValue) {
          $identifier = isset($identifiers[$questionId]) ? $identifiers[$questionId] : '';
          $data = array(
            'answer_set_id' => $setId,
            'user_id' => $userId,
            'subject_id' => $subjectId,
            'question_id' => $questionId,
            'question_identifier' => $identifier,
            'answer_value' => $answerValue,
            'answer_deactivated' => 0,
            'answer_timestamp' => $timestamp
          );
          $success = $this->databaseInsertRecord($this->_tableAnswer, NULL, $data);
          if ($success === FALSE) {
            $result = FALSE;
          }
        }
      }
    }
    return $result;
  }

  /**
  * Delete answers by user id and subject id
  *
  * Will be called before saving new answers by the same user for the same subject
  *
  * @param string $userId
  * @param string $subjectId
  */
  protected function deleteAnswersByUserAndSubject($userId, $subjectId) {
    $success = $this->databaseDeleteRecord(
      $this->_tableAnswer,
      array('user_id' => $userId, 'subject_id' => $subjectId)
    );
    if (FALSE !== $success) {
      return TRUE;
    }
    return FALSE;
  }

  /**
  * Deactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersByUserId($userId) {
    $deactivationTime = time();
    $success = $this->databaseUpdateRecord(
      $this->_tableAnswerSet,
      array('answer_deactivated' => $deactivationTime),
      'user_id',
      $userId
    );
    if ($success === FALSE) {
      return FALSE;
    }
    return TRUE;
  }

  /**
  * Reactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersByUserId($userId) {
    $success = $this->databaseUpdateRecord(
      $this->_tableAnswerSet,
      array('answer_deactivated' => 0),
      'user_id',
      $userId
    );
    if ($success === FALSE) {
      return FALSE;
    }
    return TRUE;
  }

  /**
  * Deactivate answers by subject id
  *
  * @param string $subjectId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersBySubjectId($subjectId) {
    $result = FALSE;
    if ($subjectId != '') {
      $deactivationTime = time();
      $success = $this->databaseUpdateRecord(
        $this->_tableAnswerSet,
        array('answer_deactivated' => $deactivationTime),
        'subject_id',
        $subjectId
      );
      $result = (bool)$success;
    }
    return $result;
  }

  /**
  * Reactivate answers by subject id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersBySubjectId($subjectId) {
    $result = FALSE;
    if ($subjectId != '') {
      $result = FALSE !== $this->databaseUpdateRecord(
        $this->_tableAnswerSet,
        array('answer_deactivated' => 0),
        'subject_id',
        $subjectId
      );
    }
    return $result;
  }

  public function addMetaForAnswerSet($answerSetId, $key, $value) {
    $data = array(
      'answer_set_id' => $answerSetId,
      'answer_set_meta_key' => (string)$key,
      'answer_set_meta_value' => $value,
    );
    return $this->databaseInsertRecord($this->_tableAnswerSetMeta, 'answer_set_meta_id', $data);
  }

  public function getMetaForAnswerSet($answerSetId, $key = NULL) {
    $result = FALSE;
    $keyCondition = '';
    if (!is_null($key)) {
      $keyCondition = ' AND '.$this->databaseGetSqlCondition('answer_set_meta_key', $key);
    }

    $sql = "SELECT answer_set_meta_id, answer_set_id, answer_set_meta_key, answer_set_meta_value
              FROM %s
             WHERE answer_set_id = %d
                   $keyCondition
           ";
    $params = array($this->_tableAnswerSetMeta, $answerSetId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['answer_set_meta_id']] = $row;
      }
    }
    return $result;
  }

  /**
  * Returns the amount of given questionnaires in a specific timeframe
  * for a given user
  *
  * @param string $userId
  * @return array
  */
  public function getAnswerSetsInCurrentYear($userId) {
    $result = array();
    $userCondition = $this->databaseGetSqlCondition('qas.user_id', $userId);

    $timeframeCondition = $this->getYearRangeCondition($userId);

    $sql = "SELECT qas.answer_set_id
              FROM %s as qas
         LEFT JOIN %s as ps
                ON qas.user_id = ps.surfer_id
             WHERE $userCondition
               AND qas.answer_deleted = 0
               AND qas.answer_timestamp $timeframeCondition
           ";
    $params = array(
      $this->_tableAnswerSet,
      $this->_tableSurfer
    );
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[] = $row['answer_set_id'];
      }
    }
    return $result;
  }

  /**
  * Returns the condition for the timeframe
  *
  * @param string $surferId
  * @return string
  */
  public function getYearRangeCondition($surferId) {
    $timeDiff = 0;
    $surferCondition = $this->databaseGetSqlCondition('surfer_id', $surferId);

    $sql = "SELECT UNIX_TIMESTAMP(
                   CONCAT(
                    DATE_FORMAT(NOW(),'%%Y'),
                    DATE_FORMAT(
                      FROM_UNIXTIME(
                        surfer_registration),'-%%m-%%d %%H:%%i:%%s')
                      )
                    ) - UNIX_TIMESTAMP() as difference
              FROM %s
             WHERE $surferCondition";
    $params = array(
      $this->_tableSurfer
    );
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $row = $res->fetchRow(DB_FETCHMODE_ASSOC);
      $timeDiff = (int)$row['difference'];
    }
    $condition = "BETWEEN
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

    if ($timeDiff < 0) {
      $condition = "BETWEEN
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
    }
    return $condition;
  }

  /**
  * Retrieve answer sets for given surfer ids - ordered by their timestamp
  *
  * @param array $surferIds
  * @return array
  */
  public function getAnswerSetsForSurfers($surferIds) {
    $result = FALSE;

    $condition = $this->databaseGetSqlCondition('user_id', $surferIds);
    $sql = "SELECT answer_set_id, answer_timestamp, user_id, subject_id, answer_deleted,
                   answer_deactivated
              FROM %s
             WHERE $condition
             ORDER BY answer_timestamp DESC";

    $params = array(
      $this->_tableAnswerSet,
    );

    if (FALSE != ($res = $this->databaseQueryFmt($sql, $params))) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[] = $row;
      }
    }

    return $result;
  }

  /**
  * Transfer "ownership" of answer set to given surferid
  *
  * @param array $answerSetIds
  * @param string $surferId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function transferAnswerSetsToSurfer($answerSetIds, $surferId) {
    return (bool)$this->databaseUpdateRecord(
      $this->_tableAnswerSet,
      array('user_id' => $surferId),
      'answer_set_id',
      $answerSetIds
    );
  }


  /**
   * Deletes deprecated answer sets after merging
   *
   * @param array $answerSetIds
   * @param string $userId
   * @return boolean TRUE on success, FALSE otherwise
   */
  public function deleteDeprecatedAnswerSets($answerSetIds, $userId) {

    $userCondition = $this->databaseGetSqlCondition('user_id', $userId);
    $answerSetCondition = $this->databaseGetSqlCondition('answer_set_id', $answerSetIds);

    $sql = "DELETE FROM %s
            WHERE $userCondition
              AND $answerSetCondition";
    $params = array(
      $this->_tableAnswerSet,
    );
    $result = $this->databaseQueryFmtWrite($sql, $params);
    if (FALSE !== $result) {
      return TRUE;
    }
    return FALSE;
  }

}
