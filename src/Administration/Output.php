<?php
/**
* The Output class generates all kinds of listviews to be displayed in the backend. In detail
* this is the pool list, the group list and the question list. As a bonus, it generates
* the toolbar xml as well.
*
* Extend this class, if you intend to show new panels in the backend of the Questionnaire
* module. Simply write a new method that generates a listview or whatever asset you need.
* If need arises, use protected submethods.
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
* @version $Id: Output.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Include base papaya object class.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_object.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireAdministrationOutput extends base_object {

  private $_parameters = array();

  private $_images = array();

  /**
  * This method sets the parameters.
  * @param array $parameters
  */
  public function setParameters($parameters) {
    $this->_parameters = $parameters;
  }

  /**
  * This method sets the images used in the listviews.
  * @param array $images
  */
  public function setImages($images) {
    $this->_images = $images;
  }

  /**
  * This method generates a list of pools.
  *
  * @param array $pools
  * @return string
  */
  public function getPoolList($pools) {
    $result = '';
    $result .= sprintf(
      '<listview title="%s" icon="%s">'.LF,
      $this->_gt('Question pools'),
      $this->_images['items-folder']);
    $result .= '<cols>'.LF;
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Pool name')));
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Id')));
    $result .= '</cols>'.LF;
    $result .= '<items>'.LF;
    if (is_array($pools)) {
      foreach ($pools as $pool) {
        if (isset($this->_parameters['question_pool_id']) &&
            $this->_parameters['question_pool_id'] == $pool['question_pool_id']) {
          $selected = ' selected="selected"';
        } else {
          $selected = '';
        }
        $result .= sprintf(
          '<listitem title="%s" href="%s" %s>'.LF,
          papaya_strings::escapeHTMLChars($pool['question_pool_name']),
          $this->getLink(
            array('question_pool_id' => $pool['question_pool_id'], 'cmd' => 'edit_pool')
          ),
          $selected
        );
        $result .= sprintf('<subitem align="right">%d</subitem>'.LF, $pool['question_pool_id']);
        $result .= '</listitem>'.LF;
      }
    }
    $result .= '</items>'.LF;
    $result .= '</listview>'.LF;
    return $result;
  }

  /**
  * This method generates a list of groups.
  *
  * @param array $groups
  * @return string
  */
  public function getGroupList($groups) {
    $result = '';
    $result .= sprintf(
      '<listview title="%s" icon="%s">'.LF,
      $this->_gt('Question groups'),
      $this->_images['items-table']);
    $result .= '<cols>'.LF;
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Group name')));
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Id')));
    $result .= '<col />'.LF;
    $result .= '<col />'.LF;
    $result .= '</cols>'.LF;
      $result .= '<items>'.LF;
    if (is_array($groups)) {
      $i = 0;
      $count = count($groups);
      foreach ($groups as $group) {
        if (isset($this->_parameters['question_group_id']) &&
            $this->_parameters['question_group_id'] == $group['question_group_id']) {
          $selected = ' selected="selected"';
        } else {
          $selected = '';
        }
        $result .= sprintf(
          '<listitem title="%s" href="%s" %s>'.LF,
          papaya_strings::escapeHTMLChars($group['question_group_name']),
          $this->getLink(
            array(
              'cmd' => 'edit_group',
              'question_pool_id' => $group['question_pool_id'],
              'question_group_id' => $group['question_group_id']
            )
          ),
          $selected
        );
        $result .= sprintf('<subitem>%d</subitem>'.LF, $group['question_group_id']);
        if ($i == 0) {
          $result .= '<subitem />'.LF;
        } else {
          $result .= sprintf(
            '<subitem><a href="%s"><glyph src="%s" /></a></subitem>'.LF,
            $this->getLink(
              array(
                'cmd' => 'group_up',
                'question_group_id' => $group['question_group_id']
              )
            ),
            $this->_images['actions-go-up']
          );
        }
        if ($i == $count - 1) {
          $result .= '<subitem />'.LF;
        } else {
          $result .= sprintf(
            '<subitem><a href="%s"><glyph src="%s" /></a></subitem>'.LF,
            $this->getLink(
              array(
                'cmd' => 'group_down',
                'question_group_id' => $group['question_group_id']
              )
            ),
            $this->_images['actions-go-down']
          );
        }
        $result .= '</listitem>'.LF;
        $i++;
      }
    }
    $result .= '</items>'.LF;
    $result .= '</listview>'.LF;
    return $result;
  }

  /**
  * This method generates a list of questions.
  *
  * @param array $questions
  * @return string
  */
  public function getQuestionList($questions) {
    $result = '';
    $result .= sprintf(
      '<listview title="%s" icon="%s">'.LF,
      $this->_gt('Questions'),
      $this->_images['items-page']);
    $result .= '<cols>'.LF;
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Question')));
    $result .= sprintf(
      '<col>%s</col>'.LF,
      papaya_strings::escapeHTMLChars($this->_gt('Ident')));
    $result .= '<col />'.LF;
    $result .= '<col />'.LF;
    $result .= '</cols>'.LF;
      $result .= '<items>'.LF;
    if (is_array($questions)) {
      $i = 0;
      $count = count($questions);
      foreach ($questions as $question) {
        if (isset($this->_parameters['question_id']) &&
            $this->_parameters['question_id'] == $question['question_id']) {
          $selected = ' selected="selected"';
        } else {
          $selected = '';
        }
        $result .= sprintf(
          '<listitem title="%s" href="%s" %s>'.LF,
          papaya_strings::escapeHTMLChars(papaya_strings::truncate($question['question_text'], 50)),
          $this->getLink(
            array(
              'question_group_id' => $question['question_group_id'],
              'question_id' => $question['question_id'],
              'cmd' => 'edit_question'
            )),
          $selected
        );
        $result .= sprintf(
          '<subitem>%s</subitem>'.LF,
          papaya_strings::escapeHTMLChars($question['question_identifier']));
        if ($i == 0) {
          $result .= '<subitem />'.LF;
        } else {
          $result .= sprintf(
            '<subitem><a href="%s"><glyph src="%s" /></a></subitem>'.LF,
            $this->getLink(
              array(
                'cmd' => 'question_up',
                'question_id' => $question['question_id']
              )
            ),
            $this->_images['actions-go-up']
          );
        }
        if ($i == $count - 1) {
          $result .= '<subitem />'.LF;
        } else {
          $result .= sprintf(
            '<subitem><a href="%s"><glyph src="%s" /></a></subitem>'.LF,
            $this->getLink(
              array(
                'cmd' => 'question_down',
                'question_id' => $question['question_id']
              )
            ),
            $this->_images['actions-go-down']
          );
        }
        $result .= '</listitem>'.LF;
        $i++;
      }
    }
    $result .= '</items>'.LF;
    $result .= '</listview>'.LF;
    return $result;
  }

  /**
  * This method generates the toolbar XML.
  *
  * @return string
  */
  public function getToolbar() {
    include_once(PAPAYA_INCLUDE_PATH.'/system/base_btnbuilder.php');
    $toolbar = new base_btnbuilder;
    $toolbar->addButton(
      $this->_gt('Overview'),
      $this->getLink(array('cmd' => 'overview')),
      $this->_images['categories-view-list'],
      $this->_gt('Display an Overview of configured Questionnaires'),
      FALSE
    );
    $toolbar->addButton(
      $this->_gt('ext. Overview'),
      $this->getLink(array('cmd' => 'overview_extended')),
      $this->_images['categories-view-list'],
      $this->_gt('Display an extended Overview of configured Questionnaires'),
      FALSE
    );
    $toolbar->addSeperator();
    $toolbar->addButton(
      $this->_gt('Add pool'),
      $this->getLink(array('cmd' => 'add_pool')),
      $this->_images['actions-folder-add'],
      $this->_gt('Add a new question pool'),
      FALSE
    );
    if (isset($this->_parameters['question_pool_id'])) {
      $toolbar->addButton(
        $this->_gt('Copy pool'),
        $this->getLink(
          array(
            'cmd' => 'copy_pool',
            'question_pool_id' => $this->_parameters['question_pool_id']
        )),
        $this->_images['actions-edit-copy'],
        $this->_gt('Copy the selected pool'),
        FALSE
      );
      $toolbar->addButton(
        $this->_gt('Delete pool'),
        $this->getLink(
          array(
            'cmd' => 'del_pool',
            'question_pool_id' => $this->_parameters['question_pool_id']
        )),
        $this->_images['actions-folder-delete'],
        $this->_gt('Delete the selected pool'),
        FALSE
      );
      $toolbar->addSeperator();
      $toolbar->addButton(
        $this->_gt('Add group'),
        $this->getLink(
          array(
            'cmd' => 'add_group',
            'question_pool_id' => $this->_parameters['question_pool_id']
        )),
        $this->_images['actions-table-add'],
        $this->_gt('Add a new question group'),
        FALSE
      );
    }
    if (isset($this->_parameters['question_group_id'])) {
      $toolbar->addButton(
        $this->_gt('Delete group'),
        $this->getLink(
          array(
            'cmd' => 'del_group',
            'question_group_id' => $this->_parameters['question_group_id']
        )),
        $this->_images['actions-table-delete'],
        $this->_gt('Delete the selected group'),
        FALSE
      );
      $toolbar->addSeperator();
      $toolbar->addButton(
        $this->_gt('Add question'),
        $this->getLink(
          array(
            'cmd' => 'add_question',
            'question_group_id' => $this->_parameters['question_group_id']
        )),
        $this->_images['actions-page-add'],
        $this->_gt('Add a new question'),
        FALSE
      );
    }
    if (isset($this->_parameters['question_id'])) {

      $toolbar->addButton(
        $this->_gt('Delete question'),
        $this->getLink(
          array(
            'cmd' => 'del_question',
            'question_id' => $this->_parameters['question_id']
        )),
        $this->_images['actions-page-delete'],
        $this->_gt('Delete the selected question'),
        FALSE
      );
    }

    return '<menu>'.$toolbar->getXML().'</menu>';
  }
}
