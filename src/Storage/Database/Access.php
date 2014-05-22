<?php
/**
* The Storage datbase access class provides means of reading and writing data from the
* question related tables.
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
*/

/**
* Base class papaya database access
*/
require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_db.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireStorageDatabaseAccess extends base_db {

  /**
  * Configuration object
  * @var PapayaConfiguration
  */
  private $_configuration;

  /**
  * question creator object
  * @var PapayaQuestionnaireQuestionCretor
  */
  protected $_questionCreator;

  protected $_tablePool;
  protected $_tableGroup;
  protected $_tableQuestion;
  protected $_tableAnswer;
  protected $_tableAnswerSet;
  protected $_tableAnswerSetMeta;
  protected $_tableAnswerOptions;

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
    $this->_tablePool = $tablePrefix.'_questionnaire_pool';
    $this->_tableGroup = $tablePrefix.'_questionnaire_group';
    $this->_tableQuestion = $tablePrefix.'_questionnaire_question';
    $this->_tableAnswer = $tablePrefix.'_questionnaire_answer';
    $this->_tableAnswerSet = $tablePrefix.'_questionnaire_answer_set';
    $this->_tableAnswerSetMeta = $tablePrefix.'_questionnaire_answer_set_meta';
    $this->_tableAnswerOptions = $tablePrefix.'_questionnaire_answer_options';
  }

  /* Pool related methods */

  /**
  * This method retrieves data for a single pool
  *
  * @param integer $poolId
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name'))
  */
  public function getPool($poolId) {
    $result = FALSE;
    $sql = "SELECT question_pool_id, question_pool_name, question_pool_identifier
              FROM %s
             WHERE question_pool_id = %d
           ";
    $params = array($this->_tablePool, $poolId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchRow(DB_FETCHMODE_ASSOC);
    }
    return $result;
  }

  /**
  * This method retrieves a list of existing question pools
  *
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name',
  *               'question_pool_identifier' => 'p01'))
  */
  public function getPools() {
    $result = FALSE;
    $sql = "SELECT question_pool_id, question_pool_name, question_pool_identifier
              FROM %s
             ORDER BY question_pool_name ASC
           ";
    $params = array($this->_tablePool);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_pool_id']] = $row;
      }
    }
    return $result;
  }

  /**
  * This method creates a new pool
  *
  * @param array $parameters array('question_pool_name' => 'my pool')
  * @return integer
  */
  public function createPool($parameters) {
    $result = FALSE;
    $necessaryParameters = array(
      'question_pool_name',
    );
    $optionalParameters = array(
      'question_pool_identifier',
    );
    if (array() == array_diff($necessaryParameters, array_keys($parameters))) {
      foreach ($necessaryParameters as $key) {
        $data[$key] = $parameters[$key];
      }
      foreach ($optionalParameters as $key) {
        if (isset($parameters[$key])) {
          $data[$key] = $parameters[$key];
        }
      }
      $result = $this->databaseInsertRecord($this->_tablePool, 'question_pool_id', $data);
    }
    return $result;
  }

  /**
  * This method updates a pool
  *
  * @param array $parameters
  * @return integer
  */
  public function updatePool($poolId, $parameters) {
    if ($poolId > 0 && !empty($parameters)) {
      $result = FALSE;
      $validParameters = array(
        'question_pool_name',
        'question_pool_identifier',
      );
      foreach ($validParameters as $key) {
        if (isset($parameters[$key])) {
          $data[$key] = $parameters[$key];
        }
      }
      if (!empty($data) && is_array($data)) {
        $condition = array('question_pool_id' => $poolId);
        $result = $this->databaseUpdateRecord($this->_tablePool, $data, $condition);
      }
    }
    return $result;
  }

  /**
  * This method deletes a pool, its groups and questions
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deletePool($poolId) {
    $result = FALSE;
    if ($poolId > 0) {
      if ($this->deleteQuestionsInPool($poolId)) {
        if ($this->deleteGroupsInPool($poolId)) {
          $result = FALSE !== $this->databaseDeleteRecord(
            $this->_tablePool,
            array('question_pool_id' => $poolId));
        }
      }
    }
    return $result;
  }

  /* Group related methods */

  /**
  * This method retrieves a list of groups for a given pool id
  *
  * @param integer $poolId
  * @return array
  */
  public function getGroups($poolId) {
    $result = FALSE;
    $sql = "SELECT question_pool_id, question_group_id, question_group_position,
                   question_group_identifier, question_group_name, question_group_text,
                   question_group_min_answers, question_group_subtitle
              FROM %s
             WHERE question_pool_id = %d
             ORDER BY question_group_position ASC
           ";
    $params = array($this->_tableGroup, $poolId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_group_id']] = $row;
      }
    }
    return $result;
  }

  /**
  * This method retrieves a single group by its id
  *
  * @param integer $groupId
  * @return array
  */
  public function getGroup($groupId) {
    $result = FALSE;
    $sql = "SELECT question_pool_id, question_group_id, question_group_position,
                   question_group_identifier, question_group_name, question_group_text,
                   question_group_min_answers, question_group_subtitle
              FROM %s
             WHERE question_group_id = %d
           ";
    $params = array($this->_tableGroup, $groupId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchRow(DB_FETCHMODE_ASSOC);
    }
    return $result;
  }

  /**
  * This method creates a new group
  *
  * @param array $parameters
  * @return integer
  */
  public function createGroup($parameters) {
    $result = FALSE;
    $necessaryParameters = array(
      'question_group_name',
      'question_group_identifier',
      'question_pool_id',
    );
    $optionalParameters = array(
      'question_group_text',
      'question_group_position',
      'question_group_min_answers',
      'question_group_subtitle',
    );
    if (array() == array_diff($necessaryParameters, array_keys($parameters))) {
      foreach ($necessaryParameters as $key) {
        $data[$key] = $parameters[$key];
      }
      foreach ($optionalParameters as $key) {
        if (isset($parameters[$key])) {
          $data[$key] = $parameters[$key];
        }
      }
      $data['question_group_position'] =
        $this->_getNextGroupPosition($parameters['question_pool_id']);
      $result = $this->databaseInsertRecord($this->_tableGroup, 'question_group_id', $data);
    }
    return $result;
  }

  /**
  * This method updates an existing groups data
  *
  * @param integer $groupId
  * @param array $parameters
  * @return boolean
  */
  public function updateGroup($groupId, $parameters) {
    if ($groupId > 0 && !empty($parameters)) {
      $result = FALSE;
      $validParameters = array(
        'question_group_name',
        'question_group_text',
        'question_group_subtitle',
        'question_group_identifier',
        'question_group_min_answers',
        'question_pool_id',
      );
      foreach ($validParameters as $key) {
        if (isset($parameters[$key])) {
          $data[$key] = $parameters[$key];
        }
      }
      if (!empty($data) && is_array($data)) {
        $condition = array('question_group_id' => $groupId);
        $result = $this->databaseUpdateRecord($this->_tableGroup, $data, $condition);
      }
    }
    return $result;
  }

  /**
  * This method deletes all groups from a pool.
  *
  * It doesn't delete questions, so those should have been deleted before.
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deleteGroupsInPool($poolId) {
    $result = FALSE;
    if ($poolId > 0) {
      $result = FALSE !== $this->databaseDeleteRecord(
        $this->_tableGroup,
        array('question_pool_id' => $poolId));
    }
    return $result;
  }

  /**
  * This method deletes a group and its questions.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function deleteGroup($groupId) {
    $result = FALSE;
    if ($groupId > 0) {
      if ($this->deleteQuestionsInGroup($groupId)) {
        $result = FALSE !== $this->databaseDeleteRecord(
          $this->_tableGroup,
          array('question_group_id' => $groupId));
      }
    }
    return $result;
  }

  /**
  * This method retrieves the group pool id.
  *
  * @param integer $groupId
  * @return integer
  */
  protected function _getGroupPoolId($groupId) {
    $result = FALSE;
    $sql = "SELECT question_pool_id
             FROM %s
            WHERE question_group_id = %d";
    $params = array($this->_tableGroup, $groupId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  /**
  * This method retrieves the group position.
  *
  * @param integer $groupId
  * @return integer
  */
  protected function _getGroupPosition($groupId) {
    $result = FALSE;
    $sql = "SELECT question_group_position
             FROM %s
            WHERE question_group_id = %d";
    $params = array($this->_tableGroup, $groupId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  /**
  * This method finds the largest group position number and returns the next number
  * @param integer $poolId
  * @return integer
  */
  protected function _getNextGroupPosition($poolId) {
    $result = 0;
    $sql = "SELECT MAX(question_group_position)
              FROM %s
             WHERE question_pool_id = %d
           ";
    $params = array($this->_tableGroup, $poolId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = (int)$res->fetchField();
    }
    return $result + 1;
  }

  /**
  * This method moves a group up in the position list.
  *
  * It's a wrapper for {@see _moveGroup}
  *
  * @param integer $groupId
  * @param integer $steps
  * @return boolean
  */
  public function moveGroupUp($groupId, $steps = 1) {
    return $this->_moveGroup($groupId, -1 * $steps);
  }

  /**
  * This method moves a group down in the position list.
  *
  * It's a wrapper for {@see _moveGroup}
  *
  * @param integer $groupId
  * @param integer $steps
  * @return boolean
  */
  public function moveGroupDown($groupId, $steps = 1) {
    return $this->_moveGroup($groupId, $steps);
  }

  /**
  * This method moves a group in the position list.
  *
  * @param integer $groupId
  * @param integer $relativePosition
  * @return boolean
  */
  protected function _moveGroup($groupId, $relativePosition) {
    $result = FALSE;
    $position = (int)$this->_getGroupPosition($groupId);
    $newPosition = $position + $relativePosition;
    if ($relativePosition > 0) {
      $sql = "UPDATE %s
                 SET question_group_position = question_group_position - 1
               WHERE question_group_position > %d
                 AND question_group_position <= %d
                 AND question_pool_id = %d
             ";
    } else {
      $sql = "UPDATE %s
                 SET question_group_position = question_group_position + 1
               WHERE question_group_position < %d
                 AND question_group_position >= %d
                 AND question_pool_id = %d
             ";
    }
    $params = array(
      $this->_tableGroup,
      $position,
      $newPosition,
      $this->_getGroupPoolId($groupId),
    );
    if ($this->databaseQueryFmtWrite($sql, $params)) {
      $data = array(
        'question_group_position' => $newPosition
      );
      $condition = array(
        'question_group_id' => $groupId
      );
      $result = FALSE !== $this->databaseUpdateRecord($this->_tableGroup, $data, $condition);
    }
    return $result;
  }

  /* Question related methods */

  /**
  * This method retrieves a list of questions for a given pool id.
  *
  * Default sort field is question_position, question_identifier and question_text are
  * allowed as well
  *
  * @param integer $groupId
  * @param string $sortField
  * @param string $order
  * @return array
  */
  public function getQuestions($groupId, $sortField = 'question_position', $order = 'ASC') {
    $validSortFields = array(
      'question_identifier' => 1,
      'question_text' => 1,
      'question_position' => 1,
    );
    $order = (strtolower($order) == 'DESC') ? 'DESC' : 'ASC';
    $sortField = (isset($validSortFields[$sortField])) ? $sortField : 'question_id';

    $result = FALSE;
    $sql = "SELECT question_id, question_position, question_group_id, question_identifier,
                   question_type, question_text, question_answer_data
              FROM %s
             WHERE question_group_id = %d
             ORDER BY $sortField $order
           ";
    $params = array($this->_tableQuestion, $groupId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = array();
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_id']] = $row;
      }
    }
    return $result;
  }

  /**
  * This method retrieves questions by a list of their ids
  *
  * @param array $questionIds
  * @return array
  */
  public function getQuestionsByIds($questionIds) {
    $result = FALSE;
    if ((is_array($questionIds) && count($questionIds) > 0) || $questionIds > 0) {
      $questionCondition = $this->databaseGetSQLCondition('question_id', $questionIds);
      $sql = "SELECT question_id, question_position, question_group_id, question_identifier,
                     question_type, question_text, question_answer_data
                FROM %s
               WHERE $questionCondition
             ";
      $params = array($this->_tableQuestion);
      if ($res = $this->databaseQueryFmt($sql, $params)) {
        $result = array();
        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
          $result[$row['question_id']] = $row;
        }
      }
    }
    return $result;
  }

  /**
  * This method retrieves questions by a list of their identifiers
  *
  * @param array $identifiers
  * @return array
  */
  public function getQuestionsByIdentifiers($identifiers) {
    $result = FALSE;
    if ((is_array($identifiers) && count($identifiers) > 0) || $identifiers > 0) {
      $questionCondition = $this->databaseGetSQLCondition('question_identifier', $identifiers);
      $sql = "SELECT question_id, question_position, question_group_id, question_identifier,
                     question_type, question_text, question_answer_data
                FROM %s
               WHERE $questionCondition
             ";
      $params = array($this->_tableQuestion);
      if ($res = $this->databaseQueryFmt($sql, $params)) {
        $result = array();
        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
          $result[$row['question_id']] = $row;
        }
      }
    }
    return $result;
  }

  /**
  * This method retrieves a single question by its id.
  *
  * @param integer $questionId
  * @return PapayaQuestionnaireQuestion
  */
  public function getQuestion($questionId) {
    $result = array();
    $sql = "SELECT question_id, question_position, question_group_id, question_identifier,
                   question_type, question_text, question_answer_data
              FROM %s
             WHERE question_id = %d
           ";
    $params = array($this->_tableQuestion, $questionId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $questionData = $res->fetchRow(DB_FETCHMODE_ASSOC);
      $questionCreator = $this->getQuestionCreator();
      if ($question = $questionCreator->getQuestionObject($questionData['question_type'])) {
        $question->loadFromData($questionData);
        $answers = $this->_getQuestionAnswers($questionId);
        $question->setAnswers($answers);

        $result = $question;
        // load question answer options
      } else {
        throw new LogicException(
          sprintf('Question type "%s" not available.', $questionData['question_type'])
        );
      }
    }
    return $result;
  }

  /**
  * This method creates a new question.
  *
  * @param array $parameters
  * @return integer
  */
  public function createQuestion($question) {
    $result = FALSE;
    $data = array(
      'question_group_id' => (int)$question->getGroupId(),
      'question_identifier' => (string)$question->getIdentifier(),
      'question_type' => $question->getType(),
      'question_text' => (string)$question->getText(),
      'question_answer_data' => $question->getConfigurationDataXML(),
      'question_position' => $this->_getNextQuestionPosition($question->getGroupId()),
    );
    if ($result = $this->databaseInsertRecord($this->_tableQuestion, 'question_id', $data)) {
      $question->setId($result);
      $this->_saveQuestionAnswers($question);
    }
    return $result;
  }


  protected function _getQuestionAnswers($questionId) {
    $result = array();
    $sql = "SELECT answer_choice_text, answer_choice_value, answer_choice_id
              FROM %s
             WHERE question_id = %d
           ";
    $params = array($this->_tableAnswerOptions, $questionId);

    if ($res = $this->databaseQueryFmt($sql, $params)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['answer_choice_id']] = array(
          'value' => $row['answer_choice_value'],
          'text' => $row['answer_choice_text'],
        );
      }
    }
    return $result;
  }

  protected function _deleteQuestionAnswers($questionId) {
    $result = FALSE;
    if ($questionId > 0) {
      $condition = array('question_id' => $questionId);
      $result = FALSE !== $this->databaseDeleteRecord($this->_tableAnswerOptions, $condition);
    }
    return $result;
  }

  /**
  * This method updates an existing question
  *
  * @param integer $questionId
  * @param array $parameters
  * @return boolean
  */
  public function updateQuestion($question) {
    $result = FALSE;
    if ($question->getId() > 0) {
      $data = array(
        'question_group_id' => (int)$question->getGroupId(),
        'question_identifier' => (string)$question->getIdentifier(),
        'question_type' => $question->getType(),
        'question_text' => (string)$question->getText(),
        'question_answer_data' => $question->getConfigurationDataXML(),
      );
      $condition = array('question_id' => $question->getId());
      if (FALSE !== $this->databaseUpdateRecord($this->_tableQuestion, $data, $condition)) {
        $result = $this->_saveQuestionAnswers($question);
      }
    }
    return $result;
  }

  protected function _saveQuestionAnswers($question) {
    $result = FALSE;
    $oldAnswers = $this->_getQuestionAnswers($question->getId());
    $answers = $question->getAnswers();

    $count = max(array(count($oldAnswers), count($answers)));
    $oldAnswerIds = array_keys($oldAnswers);
    for ($i = 0; $i <= $count; $i++) {
      $data = array();
      $position = $i + 1;
      if (isset($answers[$position]) &&
          isset($answers[$position]['text']) &&
          $answers[$position]['text'] != '') {
        $data = array(
          'answer_choice_text' => $answers[$position]['text'],
          'answer_choice_value' => $answers[$position]['value'],
        );
      }
      if (isset($oldAnswerIds[$i]) && isset($oldAnswers[$oldAnswerIds[$i]])) {
        // there is an old value for this answer option
        $condition = array('answer_choice_id' => $oldAnswerIds[$i]);
        if (!empty($data)) {
          // new value exists, so update existing answer option record
          $result = FALSE !==
            $this->databaseUpdateRecord($this->_tableAnswerOptions, $data, $condition);
        } else {
          // delete no longer existing answer option record
          $result = FALSE !== $this->databaseDeleteRecord($this->_tableAnswerOptions, $condition);
        }
      } else {
        // there is no old value for this answer option, insert record
        if (!empty($data)) {
          $data['question_id'] = $question->getId();
          $result = FALSE !==
            $this->databaseInsertRecord($this->_tableAnswerOptions, NULL, $data);
        }
      }
    }
    return $result;
  }

  /**
  * This method deletes an existing question.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function deleteQuestion($questionId) {
    $result = FALSE;
    if ($questionId > 0) {
      $this->_deleteQuestionAnswers($questionId);
      $result = FALSE !== $this->databaseDeleteRecord(
        $this->_tableQuestion,
        array('question_id' => $questionId));
    }
    return $result;
  }

  /**
  * This method deletes all questions from a given group.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function deleteQuestionsInGroup($groupId) {
    $result = FALSE;
    if ($groupId > 0) {
      $result = FALSE !== $this->databaseDeleteRecord(
        $this->_tableQuestion,
        array('question_group_id' => $groupId));
    }
    return $result;
  }

  /**
  * This method deletes all questions from a given pool
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deleteQuestionsInPool($poolId) {
    $sql = "DELETE
              FROM %s
             WHERE question_group_id IN (
                   SELECT question_group_id
                     FROM %s
                    WHERE question_pool_id = %d)
           ";
    $params = array(
      $this->_tableQuestion,
      $this->_tableGroup,
      $poolId
    );
    $result = $this->databaseQueryFmtWrite($sql, $params);
    return $result;
  }

  /**
  * This method retrieves the group id of a question
  *
  * @param integer $questionId
  * @return integer
  */
  protected function _getQuestionGroupId($questionId) {
    $result = FALSE;
    $sql = "SELECT question_group_id
             FROM %s
            WHERE question_id = %d";
    $params = array($this->_tableQuestion, $questionId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  /**
  * This method retrieves the position of a question
  *
  * @param integer $questionId
  * @return integer
  */
  protected function _getQuestionPosition($questionId) {
    $result = FALSE;
    $sql = "SELECT question_position
             FROM %s
            WHERE question_id = %d";
    $params = array($this->_tableQuestion, $questionId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = $res->fetchField();
    }
    return $result;
  }

  /**
  * This method finds the largest used position number and returns the next number.
  *
  * @param integer $groupId
  * @return integer
  */
  protected function _getNextQuestionPosition($groupId) {
    $result = FALSE;
    $sql = "SELECT MAX(question_position)
             FROM %s
            WHERE question_group_id = %d";
    $params = array($this->_tableQuestion, $groupId);
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      $result = (int)$res->fetchField();
    }
    return $result + 1;
  }

  /**
  * This method moves a question up in the position list.
  *
  * It's a wrapper for {@see _moveQuestion}
  *
  * @param integer $questionId
  * @param integer $steps
  * @return boolean
  */
  public function moveQuestionUp($questionId, $steps = 1) {
    return $this->_moveQuestion($questionId, -1 * $steps);
  }

  /**
  * This method moves a question down in the position list.
  *
  * It's a wrapper for {@see _moveQuestion}
  *
  * @param integer $questionId
  * @param integer $steps
  * @return boolean
  */
  public function moveQuestionDown($questionId, $steps = 1) {
    return $this->_moveQuestion($questionId, $steps);
  }

  /**
  * This method moves a group in the position list.
  *
  * @param integer $questionId
  * @param integer $relativePosition
  * @return boolean
  */
  protected function _moveQuestion($questionId, $relativePosition) {
    $result = FALSE;
    $position = (int)$this->_getQuestionPosition($questionId);
    $newPosition = $position + $relativePosition;
    if ($relativePosition > 0) {
      $sql = "UPDATE %s
                 SET question_position = question_position - 1
               WHERE question_position > %d
                 AND question_position <= %d
                 AND question_group_id = %d
             ";
    } else {
      $sql = "UPDATE %s
                 SET question_position = question_position + 1
               WHERE question_position < %d
                 AND question_position >= %d
                 AND question_group_id = %d
             ";
    }
    $params = array(
      $this->_tableQuestion,
      $position,
      $newPosition,
      $this->_getQuestionGroupId($questionId),
    );
    if ($this->databaseQueryFmtWrite($sql, $params)) {
      $data = array(
        'question_position' => $newPosition
      );
      $condition = array(
        'question_id' => $questionId
      );
      $result = FALSE !== $this->databaseUpdateRecord($this->_tableQuestion, $data, $condition);
    }
    return $result;
  }


  /**
  *
  * @param PapayaQuestionnaireQuestionCreator $questionCreator
  */
  public function setQuestionCreator(PapayaQuestionnaireQuestionCreator $questionCreator) {
    $this->_questionCreator = $questionCreator;
  }

  /**
  *
  * @return PapayaQuestionnaireQuestionCreator
  */
  public function getQuestionCreator() {
    if (empty($this->_questionCreator)) {
      include_once(dirname(__FILE__).'/../../Question/Creator.php');
      $this->setQuestionCreator(new PapayaQuestionnaireQuestionCreator);
    }
    return $this->_questionCreator;
  }

}
