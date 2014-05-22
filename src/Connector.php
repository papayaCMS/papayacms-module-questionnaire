<?php
/**
* The Connector class provides an interface to interact with the Questionnaire module.
*
* GUID: 36d94a3fdaf122d8214776b34ffdb012
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
* @version $Id: Connector.php 2 2013-12-09 16:39:31Z weinert $
* @tutorial Commercial/Questionnaire/PapayaQuestionnaire.pkg
* @tutorial Commercial/Questionnaire/PapayaQuestionnaireConnector.cls
*/

/**
* Basic class base_connector
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_connector.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireConnector extends base_connector {
  /**
  * Configuration object
  * @var PapayaConfiguration
  */
  protected $_configuration = NULL;

  /**
  * Storage object
  * @var PapayaQuestionnaireStorage
  */
  protected $_storage = NULL;

  /**
  * Question creator object
  * @var PapayaQuestionnaireQuestionCreator
  */
  protected $_creator = NULL;

  /**
  * Object used as a service to store and retrieve answers
  * @var PapayaQuestionnaireAnswer
  */
  protected $_answerObject = NULL;

  /**
  * Copier object that performs copying actions
  * @var PapayaQuestionnaireStorageCopier
  */
  protected $_copier = NULL;

  /**
  * Module object to get module options
  * @var object base_module_options
  */
  protected $_moduleOptionsObject = NULL;

  /**
  * Module options
  *
  * @var array $pluginOptionFields
  */
  var $pluginOptionFields = array(
    'answerset_limit' => array(
      'Answer set limit',
      'isNum',
      TRUE,
      'input',
      10
    )
  );

  /**
  * Set configuration object
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    $this->_configuration = $configuration;
  }

  /**
  * This method sets the storage object.
  *
  * This dependency injection is used for testing. Usually _initStorage will be triggered.
  *
  * @param PapayaQuestionnaireStorage $storageObject
  * @return boolean
  */
  public function setStorage($storageObject) {
    $result = FALSE;
    if ($storageObject instanceof PapayaQuestionnaireStorage) {
      $this->_storage = $storageObject;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method initializes the storage object, whenever it wasn't set from outside.
  *
  * @return PapayaQuestionnaireStorage
  */
  public function getStorage() {
    if (!is_object($this->_storage)) {
      include_once(dirname(__FILE__).'/Storage.php');
      $this->_storage = new PapayaQuestionnaireStorage();
      $this->_storage->setConfiguration($this->_configuration);
    }
    return $this->_storage;
  }

  /**
  * This method sets the question creator object.
  *
  * This dependency injection is used for testing. Usually _initQuestionCreator will be triggered.
  *
  * @param PapayaQuestionnaireQuestionCreator $creatorObject
  * @return boolean
  */
  public function setQuestionCreator($creatorObject) {
    $result = FALSE;
    if ($creatorObject instanceof PapayaQuestionnaireQuestionCreator) {
      $this->_creator = $creatorObject;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method initializes the question creator object, whenever it wasn't set from outside.
  *
  * @return PapayaQuestionnaireQuestionCreator
  */
  public function getQuestionCreator() {
    if (!is_object($this->_creator)) {
      include_once(dirname(__FILE__).'/Question/Creator.php');
      $this->_creator = new PapayaQuestionnaireQuestionCreator();
      $this->_creator->setConfiguration($this->_configuration);
    }
    return $this->_creator;
  }

  /**
  * Set the storage/retrieval handler for answers
  *
  * Can be used for testing as well as for using an alternative answer handler
  *
  * @param PapayaQuestionnaireAnswer $answerObject
  */
  public function setAnswerObject($answerObject) {
    $this->_answerObject = $answerObject;
  }

  /**
  * Get (and, if necessary, initialize) the answer handler object
  *
  * @return PapayaQuestionnaireAnswer
  */
  public function getAnswerObject() {
    if (!is_object($this->_answerObject)) {
      include_once(dirname(__FILE__).'/Answer.php');
      $this->_answerObject = new PapayaQuestionnaireAnswer();
      $this->_answerObject->setConfiguration($this->_configuration);
    }
    return $this->_answerObject;
  }

  /**
  * This method retrieves a list of existing question pools
  *
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name'))
  */
  public function getPools() {
    $storage = $this->getStorage();
    return $storage->getPools();
  }

  /**
  * This method retrieves data for a single pool
  *
  * @param integer $poolId
  * @return array array(1 => array('question_pool_id' => 1, 'question_pool_name' => 'name'))
  */
  public function getPool($poolId) {
    $storage = $this->getStorage();
    return $storage->getPool($poolId);
  }

  /**
  * This method creates a new pool
  *
  * @param array $parameters array('question_pool_name' => 'my pool')
  * @return integer
  */
  public function createPool($parameters) {
    $storage = $this->getStorage();
    return $storage->createPool($parameters);
  }

  /**
  * This method updates a pool
  *
  * @param array $parameters
  * @return boolean
  */
  public function updatePool($poolId, $parameters) {
    $storage = $this->getStorage();
    return $storage->updatePool($poolId, $parameters);
  }

  /**
  * This method sets the copier object.
  * @param PapayaQuestionnaireStorageCopier $copier
  */
  public function setCopier($copier) {
    $result = FALSE;
    if (is_object($copier)) {
      $this->_copier = $copier;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves the copier object and initializes it if necessary.
  * @return PapayaQuestionnaireStorageCopier
  */
  public function getCopier() {
    if (!(isset($this->_copier) && is_object($this->_copier))) {
      include_once(dirname(__FILE__).'/Storage/Copier.php');
      $this->_copier = new PapayaQuestionnaireStorageCopier;
      $this->_copier->setConnector($this);
    }
    return $this->_copier;
  }

  /**
  * This method copies a pool
  *
  * @param integr $poolId
  * @return integer
  */
  public function copyPool($poolId) {
    $poolCopier = $this->getCopier();
    return $poolCopier->copyPool($poolId);
  }

  /**
  * This method deletes a pool, its groups and questions
  *
  * @param integer $poolId
  * @return boolean
  */
  public function deletePool($poolId) {
    $storage = $this->getStorage();
    return $storage->deletePool($poolId);
  }

  /**
  * This method retrieves a list of groups for a given pool id
  *
  * @param integer $poolId
  * @return array question_pool_id, question_group_id, question_group_position,
  *               question_group_identifier, question_group_name
  */
  public function getGroups($poolId) {
    $storage = $this->getStorage();
    return $storage->getGroups($poolId);
  }

  /**
  * This method retrieves a single group by its id
  *
  * @param integer $groupId
  * @return array question_pool_id, question_group_id, question_group_position,
  *               question_group_identifier, question_group_name
  */
  public function getGroup($groupId) {
    $storage = $this->getStorage();
    return $storage->getGroup($groupId);
  }

  /**
  * This method creates a new group
  *
  * @param array $parameters
  * @return integer
  */
  public function createGroup($parameters) {
    $storage = $this->getStorage();
    return $storage->createGroup($parameters);
  }

  /**
  * This method updates data of an existing group
  *
  * @param array $parameters
  * @return integer
  */
  public function updateGroup($groupId, $parameters) {
    $storage = $this->getStorage();
    return $storage->updateGroup($groupId, $parameters);
  }

  /**
  * This method creates a new group
  *
  * @param array $parameters question_group_name, question_group_identifier,
  *              question_pool_id
  * @return integer
  */
  public function deleteGroup($groupId) {
    $storage = $this->getStorage();
    return $storage->deleteGroup($groupId);
  }

  /**
  * This method moves a group up in the position list.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function moveGroupUp($groupId) {
    $storage = $this->getStorage();
    return $storage->moveGroupUp($groupId);
  }

  /**
  * This method moves a group down in the position list.
  *
  * @param integer $groupId
  * @return boolean
  */
  public function moveGroupDown($groupId) {
    $storage = $this->getStorage();
    return $storage->moveGroupDown($groupId);
  }

  /**
  * This method retrieves a list of questions for a given pool id.
  *
  * Default sort field is question_position, question_identifier and question_text are
  * allowed as well
  *
  * @param integer $groupId
  * @param string $sortField
  * @param string $order
  * @return array question_id, question_position, question_group_id, question_identifier,
  *               question_type, question_text, question_answer_data
  */
  public function getQuestions($groupId, $sortField = 'question_position', $order = 'ASC') {
    $storage = $this->getStorage();
    return $storage->getQuestions($groupId, $sortField, $order);
  }

  /**
  * This method retrieves a list of questions by their ids
  *
  * @param array $questionIds
  * @return array
  */
  public function getQuestionsByIds($questionIds) {
    $storage = $this->getStorage();
    return $storage->getQuestionsByIds($questionIds);
  }

  /**
  * This method retrieves a list of questions by their ids
  *
  * @param array $questionIds
  * @return array
  */
  public function getQuestionsByIdentifiers($identifiers) {
    $storage = $this->getStorage();
    return $storage->getQuestionsByIdentifiers($identifiers);
  }

  /**
  * This method retrieves a single question.
  *
  * @param integer $questionId
  * @return array
  */
  public function getQuestion($questionId) {
    $storage = $this->getStorage();
    return $storage->getQuestion($questionId);
  }

  /**
  * This method creates a new question.
  *
  * @param array $parameters question_group_id, question_identifier, question_type,
  *              question_text, question_answer_data
  * @return integer
  */
  public function createQuestion($question) {
    $storage = $this->getStorage();
    return $storage->createQuestion($question);
  }

  /**
  * This method updates an existing question.
  *
  * @param integer $questionId
  * @param array $parameters
  * @return boolean
  */
  public function updateQuestion($question) {
    $storage = $this->getStorage();
    return $storage->updateQuestion($question);
  }

  /**
  * This method deletes an existing question.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function deleteQuestion($questionId) {
    $storage = $this->getStorage();
    return $storage->deleteQuestion($questionId);
  }

  /**
  * This method moves a question up in the position list.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function moveQuestionUp($questionId) {
    $storage = $this->getStorage();
    return $storage->moveQuestionUp($questionId);
  }

  /**
  * This method moves a question down in the position list.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function moveQuestionDown($questionId) {
    $storage = $this->getStorage();
    return $storage->moveQuestionDown($questionId);
  }

  /**
  * This method retrieves a question object of the given type.
  *
  * @param string $questionType
  */
  public function getQuestionObject($questionType) {
    $creator = $this->getQuestionCreator();
    return $creator->getQuestionObject($questionType);
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
    $answerObject = $this->getAnswerObject();
    return $answerObject->getActiveAnswersByUserAndSubject(
      $userId,
      $subjectId,
      $questionIds
    );
  }

  /**
   * Get answers matching a filter
   * @param array $filter array of database field => value(s)
   * @return array matching answers
   */
  public function getAnswers($filter) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswers($filter);
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
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswersByUserAndSubject($userId, $subjectId, $questionIds);
  }

  /**
  * Get active answers by subject id
  *
  * @TODO no reference found, this method looks unused, please verify/falsify
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
    $answerObject = $this->getAnswerObject();
    return $answerObject->getActiveAnswersBySubjectId($subjectId, $byUser);
  }

  /**
  * Get active subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc Sort ascendent
  * @return array
  */
  public function getActiveSubjectIdsByUser($userId, $asc = FALSE) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getActiveSubjectIdsByUser($userId, $asc);
  }

  /**
  * Get subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps and deletion timestamp.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc
  * @return array
  */
  public function getSubjectIdsByUser($userId, $asc = FALSE) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getSubjectIdsByUser($userId, $asc);
  }

  /**
   * Get answer sets (=filled out questionnaires)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @param ASC|DESC $sort sort order
   * @return array answer sets, key is the answer_set_id, empty array if nothing found
   */
  public function getAnswerSets($filter, $sort = 'ASC') {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswerSets($filter, $sort);
  }

  /**
   * Mark an answer set as deleted
   * @param string $answerSetId answer set id of the answer
   * @return boolean TRUE on success
   */
  public function markAnswerSetDeleted($answerSetId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->markAnswerSetDeleted($answerSetId);
  }

  /**
   * Get answer options (=possible answers for questions)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @return array answer options, key is the answer_choice_id, empty array if nothing found
   */
  public function getAnswerOptions($filter) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswerOptions($filter);
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
    $answerObject = $this->getAnswerObject();
    return $answerObject->getActiveSubjectIdsByMetaKeyValue($userId, $metaKey, $metaValue);
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
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswerTimestampByUserAndSubject($userId, $subjectId);
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
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function saveAnswersByUserAndSubject($userId, $subjectId, $answers) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->saveAnswersByUserAndSubject($userId, $subjectId, $answers);
  }

  public function saveAnswersOfUserForSubject($userId, $subjectId, $answers, $answerSetId = NULL) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->saveAnswersOfUserForSubject($userId, $subjectId, $answers, $answerSetId);
  }


  /**
  * Deactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersByUserId($userId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->deactivateAnswersByUserId($userId);
  }

  /**
  * Reactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersByUserId($userId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->activateAnswersByUserId($userId);
  }

  /**
  * Deactivate answers by subject id
  *
  * @param string $subjectId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersBySubjectId($subjectId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->deactivateAnswersBySubjectId($subjectId);
  }

  /**
  * Reactivate answers by subject id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersBySubjectId($subjectId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->activateAnswersBySubjectId($subjectId);
  }

  public function addMetaForAnswerSet($answerSetId, $key, $value) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->addMetaForAnswerSet($answerSetId, $key, $value);
  }

  public function getMetaForAnswerSet($answerSetId, $key = NULL) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getMetaForAnswerSet($answerSetId, $key);
  }

    /**
  * Sets the options module object.
  *
  * @param object $object
  */
  public function setModuleOptionsObject($object) {
    $this->_moduleOptionsObject = $object;
  }

  /**
  * Get/Sets the options module object.
  *
  * @param base_module_options $moduleOptions
  * @return base_module_options $object
  */
  public function moduleOptions(base_module_options $moduleOptions = NULL) {
    if (!is_null($moduleOptions)) {
      $this->_moduleOptionsObject = $moduleOptions;
    }
    if (is_null($this->_moduleOptionsObject)) {
      $this->_moduleOptionsObject = new base_module_options();
    }
    return $this->_moduleOptionsObject;
  }

  /**
  * Retrieves defined answer set limit from module option
  * @param integer $default
  * @return integer
  */
  public function getAnswerSetLimit($default = 12) {
    $result = $this->moduleOptions()->readOption(
      '36d94a3fdaf122d8214776b34ffdb012',
      'answerset_limit'
    );
    if (empty($result)) {
      $result = $default;
    }
    return $result;
  }

  /**
  * Returns an array with answer set ids of the current year for a given user
  *
  * @param string $userId
  * @return array
  */
  public function getCurrentYearAnswerSets($userId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswerSetsInCurrentYear($userId);
  }

  /**
  * Retrieve answer sets for given surfer ids - ordered by their timestamp
  *
  * @param array $surferIds
  * @return array
  */
  public function getAnswerSetsForSurfers($surferIds) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->getAnswerSetsForSurfers($surferIds);
  }

  /**
  * Transfer "ownership" of answer set to given surferid
  *
  * @param array $answerSetIds
  * @param string $surferId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function transferAnswerSetsToSurfer($answerSetIds, $surferId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->transferAnswerSetsToSurfer($answerSetIds, $surferId);
  }

  /**
   * Deletes deprecated answer sets after merging
   *
   * @param array $answerSetIds
   * @param string $userId
   * @return boolean TRUE on success, FALSE otherwise
   */
  public function deleteDeprecatedAnswerSets($answerSetIds, $userId) {
    $answerObject = $this->getAnswerObject();
    return $answerObject->deleteDeprecatedAnswerSets($answerSetIds, $userId);
  }
}
