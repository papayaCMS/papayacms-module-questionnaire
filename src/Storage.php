<?php
/**
* The Storage class provides means of reading and writing data from the question related tables.
*
* Actually it forwards any requests to the Access class, just because.
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
* @version $Id: Storage.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireStorage {
  /**
  * Configuration object
  * @var PapayaConfiguration
  */
  private $_configuration = NULL;

  /**
  * Database access object
  * @var PapayaQuestionnaireStorageDatabaseAccess
  */
  private $_databaseAccessObject = NULL;

  /* Organizational methods (object setup) */

  /**
  * Set configuration object
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    $this->_configuration = $configuration;
  }

  /**
  * Set database access object
  *
  * @param PapayaQuestionnaireStorageDatabaseAccess $databaseAccessObject
  */
  public function setDatabaseAccessObject($databaseAccessObject) {
    $this->_databaseAccessObject = $databaseAccessObject;
  }

  /**
  * Get (and, if necessary, initialize) the database access object
  *
  * @return PapayaQuestionnaireStorageDatabaseAccess
  */
  public function getDatabaseAccessObject() {
    if (!is_object($this->_databaseAccessObject)) {
      include_once(dirname(__FILE__).'/Storage/Database/Access.php');
      $this->_databaseAccessObject = new PapayaQuestionnaireStorageDatabaseAccess();
      $this->_databaseAccessObject->setConfiguration($this->_configuration);
    }
    return $this->_databaseAccessObject;
  }

  /* Pool related methods */

  /**
  * This method retrieves a list of existing question pools
  *
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name'))
  */
  public function getPools() {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getPools();
  }

  /**
  * This method retrieves data for a single pool
  *
  * @param integer $poolId
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name'))
  */
  public function getPool($poolId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getPool($poolId);
  }

  /**
  * This method creates a new pool
  *
  * @param array $parameters array('question_pool_name' => 'my pool')
  * @return integer
  */
  public function createPool($parameters) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->createPool($parameters);
  }

  /**
  * This method updates a pool
  *
  * @param array $parameters
  * @return integer
  */
  public function updatePool($poolId, $parameters) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->updatePool($poolId, $parameters);
  }

  /**
  * This method deletes a pool, its groups and questions
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deletePool($poolId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deletePool($poolId);
  }

  /* Group related methods */

  /**
  * This method retrieves a list of groups for a given pool id
  *
  * @param integer $poolId
  * @return array
  */
  public function getGroups($poolId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getGroups($poolId);
  }

  /**
  * This method retrieves a single group by its id
  *
  * @param integer $groupId
  * @return array
  */
  public function getGroup($groupId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getGroup($groupId);
  }

  /**
  * This method creates a new group
  *
  * @param array $parameters
  * @return integer
  */
  public function createGroup($parameters) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->createGroup($parameters);
  }

  /**
  * This method updates an existing groups data
  *
  * @param integer $groupId
  * @param array $parameters
  * @return boolean
  */
  public function updateGroup($groupId, $parameters) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->updateGroup($groupId, $parameters);
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
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteGroupsInPool($poolId);
  }

  /**
  * This method deletes a group and its questions.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function deleteGroup($groupId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteGroup($groupId);
  }

  /**
  * This method moves a group up in the position list.
  *
  * @param integer $groupId
  * @param integer $steps
  * @return boolean
  */
  public function moveGroupUp($groupId, $steps = 1) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->moveGroupUp($groupId, $steps);
  }

  /**
  * This method moves a group down in the position list.
  *
  * @param integer $groupId
  * @param integer $steps
  * @return boolean
  */
  public function moveGroupDown($groupId, $steps = 1) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->moveGroupDown($groupId, $steps);
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
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getQuestions($groupId, $sortField, $order);
  }

  /**
  * This method retrieves a list of questions by their ids
  *
  * @param arary $questionIds
  * @return array
  */
  public function getQuestionsByIds($questionIds) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getQuestionsByIds($questionIds);
  }

  /**
   * Get a list of questions by their identifiers
   * @param array $identifiers array with identifiers
   * @return array
   */
  public function getQuestionsByIdentifiers($identifiers) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getQuestionsByIdentifiers($identifiers);
  }

  /**
  * This method retrieves a single question by its id.
  *
  * @param integer $questionId
  * @return array
  */
  public function getQuestion($questionId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getQuestion($questionId);
  }

  /**
  * This method creates a new question.
  *
  * @param array $parameters
  * @return integer
  */
  public function createQuestion($question) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->createQuestion($question);
  }

  /**
  * This method updates an existing question.
  *
  * @param integer $questionId
  * @param array $parameters
  * @return boolean
  */
  public function updateQuestion($question) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->updateQuestion($question);
  }

  /**
  * This method deletes an existing question.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function deleteQuestion($questionId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteQuestion($questionId);
  }

  /**
  * This method deletes all questions from a given group.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function deleteQuestionsInGroup($groupId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteQuestionsInGroup($groupId);
  }

  /**
  * This method deletes all questions from a given pool
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deleteQuestionsInPool($poolId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteQuestionsInPool($poolId);
  }

  /**
  * This method moves a question up in the position list.
  *
  * @param integer $questionId
  * @param integer $steps
  * @return boolean
  */
  public function moveQuestionUp($questionId, $steps = 1) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->moveQuestionUp($questionId, $steps);
  }

  /**
  * This method moves a question down in the position list.
  *
  * @param integer $questionId
  * @param integer $steps
  * @return boolean
  */
  public function moveQuestionDown($questionId, $steps = 1) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->moveQuestionDown($questionId, $steps);
  }
}
