<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

require_once(dirname(__FILE__).'/../../src/Administration/Output.php');

class PapayaQuestionnaireAdministrationOutputTest extends PapayaTestCase {

  private $_images = array(
    'categories-view-list' => 'image.png',
    'items-folder' => 'image.png',
    'items-table' => 'image.png',
    'items-page' => 'image.png',
    'actions-edit-copy' => 'image.png',
    'actions-folder-add' => 'image.png',
    'actions-folder-delete' => 'image.png',
    'actions-table-add' => 'image.png',
    'actions-table-delete' => 'image.png',
    'actions-page-add' => 'image.png',
    'actions-page-delete' => 'image.png',
    'actions-go-down' => 'image.png',
    'actions-go-up' => 'image.png',
  );

  public function testSetParameters() {
    $output = new PapayaQuestionnaireAdministrationOutput;
    $params = array('ping' => 'pong');
    $output->setParameters($params);
    $this->assertEquals($params, $this->readAttribute($output, '_parameters'));
  }

  public function testSetImages() {
    $output = new PapayaQuestionnaireAdministrationOutput;
    $images = array('action-test' => 'test.png');
    $output->setImages($images);
    $this->assertEquals($images, $this->readAttribute($output, '_images'));
  }

  public function testGetPoolList() {
    $expected = '<listview title="Question pools" icon="image.png">
<cols>
<col>Pool name</col>
<col>Id</col>
</cols>
<items>
<listitem title="my pool" href="link.php"  selected="selected">
<subitem align="right">1</subitem>
</listitem>
<listitem title="my other pool" href="link.php" >
<subitem align="right">2</subitem>
</listitem>
</items>
</listview>
';
    $output = new PapayaQuestionnaireAdministrationOutputProxy;
    $output->setImages($this->_images);
    $output->setParameters(array('question_pool_id' => 1));
    $pools = array(
      1 => array(
        'question_pool_id' => 1,
        'question_pool_name' => 'my pool'
      ),
      2 => array(
        'question_pool_id' => 2,
        'question_pool_name' => 'my other pool'
      ),
    );
    $this->assertEquals($expected, $output->getPoolList($pools));
  }

  public function testGetGroupList() {
    $expected = '<listview title="Question groups" icon="image.png">
<cols>
<col>Group name</col>
<col>Id</col>
<col />
<col />
</cols>
<items>
<listitem title="my group" href="link.php"  selected="selected">
<subitem>1</subitem>
<subitem />
<subitem><a href="link.php"><glyph src="image.png" /></a></subitem>
</listitem>
<listitem title="my other group" href="link.php" >
<subitem>2</subitem>
<subitem><a href="link.php"><glyph src="image.png" /></a></subitem>
<subitem />
</listitem>
</items>
</listview>
';
    $output = new PapayaQuestionnaireAdministrationOutputProxy;
    $output->setImages($this->_images);
    $output->setParameters(array('question_group_id' => 1));
    $groups = array(
      1 => array(
        'question_pool_id' => 1,
        'question_group_id' => 1,
        'question_group_name' => 'my group'
      ),
      2 => array(
        'question_pool_id' => 1,
        'question_group_id' => 2,
        'question_group_name' => 'my other group'
      ),
    );
    $this->assertEquals($expected, $output->getGroupList($groups));
  }

  public function testGetQuestionList() {
    $expected = '<listview title="Questions" icon="image.png">
<cols>
<col>Question</col>
<col>Ident</col>
<col />
<col />
</cols>
<items>
<listitem title="my question" href="link.php"  selected="selected">
<subitem>q1</subitem>
<subitem />
<subitem><a href="link.php"><glyph src="image.png" /></a></subitem>
</listitem>
<listitem title="my other question" href="link.php" >
<subitem>q2</subitem>
<subitem><a href="link.php"><glyph src="image.png" /></a></subitem>
<subitem />
</listitem>
</items>
</listview>
';
    $output = new PapayaQuestionnaireAdministrationOutputProxy;
    $output->setImages($this->_images);
    $output->setParameters(array('question_id' => 1));
    $questions = array(
      1 => array(
        'question_id' => 1,
        'question_identifier' => 'q1',
        'question_group_id' => 1,
        'question_text' => 'my question'
      ),
      2 => array(
        'question_id' => 2,
        'question_identifier' => 'q2',
        'question_group_id' => 1,
        'question_text' => 'my other question'
      ),
    );
    $this->assertEquals($expected, $output->getQuestionList($questions));
  }

  public function testGetToolbar() {
    $expected = '<menu><button title="Overview" href="link.php" glyph="image.png" hint="Display an Overview of configured Questionnaires" target="_self"/>
<button title="ext. Overview" href="link.php" glyph="image.png" hint="Display an extended Overview of configured Questionnaires" target="_self"/>
<seperator/>
<button title="Add pool" href="link.php" glyph="image.png" hint="Add a new question pool" target="_self"/>
<button title="Copy pool" href="link.php" glyph="image.png" hint="Copy the selected pool" target="_self"/>
<button title="Delete pool" href="link.php" glyph="image.png" hint="Delete the selected pool" target="_self"/>
<seperator/>
<button title="Add group" href="link.php" glyph="image.png" hint="Add a new question group" target="_self"/>
<button title="Delete group" href="link.php" glyph="image.png" hint="Delete the selected group" target="_self"/>
<seperator/>
<button title="Add question" href="link.php" glyph="image.png" hint="Add a new question" target="_self"/>
<button title="Delete question" href="link.php" glyph="image.png" hint="Delete the selected question" target="_self"/>
</menu>';
    $output = new PapayaQuestionnaireAdministrationOutputProxy;
    $output->setImages($this->_images);
    $params = array(
      'question_pool_id' => 1,
      'question_group_id' => 1,
      'question_id' => 1,
    );
    $output->setParameters($params);
    $this->assertEquals($expected, $output->getToolbar());
  }

}

class PapayaQuestionnaireAdministrationOutputProxy extends PapayaQuestionnaireAdministrationOutput {

  public function getLink($params) {
    return 'link.php';
  }

}
