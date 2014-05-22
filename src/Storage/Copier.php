<?php
/**
* This class provides means of copying related records, specifically pools.
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
* @version $Id: Copier.php 2 2013-12-09 16:39:31Z weinert $
* @tutorial Commercial/Questionnaire/PapayaQuestionnaireStorageCopier.cls
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireStorageCopier {

  /**
  * Instance of the questionnaire connector
  * @var PapayaQuestionnaireConnector
  */
  private $_connector;

  /**
  * This method sets the connector object (usually only for testing purposes)
  * @param PapayaQuestionnaireConnector $connector
  */
  public function setConnector($connector) {
    $result = FALSE;
    if ($connector instanceof PapayaQuestionnaireConnector) {
      $this->_connector = $connector;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves the questionnaire connector and initializes it if necessary
  * @return PapayaQuestionnaireConnector
  */
  public function getConnector() {
    if (!(isset($this->_connector) && $this->_connector instanceof PapayaQuestionnaireConnector)) {
      include_once(dirname(__FILE__).'/../Connector.php');
      $this->_connector = new PapayaQuestionnaireConnector;
    }
    return $this->_connector;
  }

  /**
  * This method creates a copy of a pool.
  *
  * * Loading the pool, altering the name and saving it as new
  * * Loading all groups, altering pool id, saving them as new
  * * Loading all questions of a group, altering group id, saving them as new
  *
  * @param integer $poolId
  */
  public function copyPool($poolId) {
    $result = FALSE;
    $connector = $this->getConnector();
    // fetch the source pool data and pass it to the create pool method
    if ($pools = $connector->getPools()) {
      $pool = $pools[$poolId];
      $pool['question_pool_name'] = 'Copy of '.$pool['question_pool_name'];
      $newPoolId = $connector->createPool($pool);
      $result = TRUE;
      if ($groups = $connector->getGroups($poolId)) {
        foreach ($groups as $groupId => $group) {
          $group['question_pool_id'] = $newPoolId;
          if (($newGroupId = $connector->createGroup($group)) &&
              ($questions = $connector->getQuestions($groupId))) {
            foreach ($questions as $questionId => $question) {
              $question['question_group_id'] = $newGroupId;
              $newQuestionId = $connector->createQuestion($question);
            }
          }
        }
      }
    }
    return $result;
  }
}
