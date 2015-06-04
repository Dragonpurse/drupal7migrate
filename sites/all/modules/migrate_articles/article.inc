 <?php

 /**
   * @file
   * ArticleMigration.
   */

class ArticleMigration extends XMLMigration {

  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('XML feed of articles');

    $fields = array(
      'title' => t('Title'),
      'teaser' => t('Teaser'),
      'body' => t('Article Body'),
    );

    $xml_folder = DRUPAL_ROOT . '/' . drupal_get_path('module', 'migrate_articles') . '/xml/';
    $items_url = $xml_folder . 'article.xml';
    $item_xpath = '/articles/article';
    $item_ID_xpath = 'id';

    $items_class = new MigrateItemsXML($items_url, $item_xpath, $item_ID_xpath);
    $this->source = new MigrateSourceMultiItems($items_class, $fields);
    $this->destination = new MigrateDestinationNode('article');

	$this->map = new MigrateSQLMap($this->machineName, array(
		'id' => array(
			'type' => 'varchar',
			'length' => 100,
			'not null' => TRUE,
		)
	), MigrateDestinationRole::getKeySchema());

    $this->addFieldMapping('body', 'body')->arguments(array('format' => 'filtered_html'))->xpath('body');
    $this->addFieldMapping('body:summary', 'teaser')->arguments(array('format' => 'filtered_html'))->xpath('teaser');
    $this->addFieldMapping('title', 'title')->xpath('title');
    $this->addFieldMapping('is_new')->defaultValue(TRUE);
    $this->addFieldMapping('status')->defaultValue(1);
  }


	/* Implement a prepare method to manipulate the node information before saving */
	public function prepare($node, stdClass $row) {
		global $user;
		dpm($user);
		$node->uid = $user->uid;
		$node->body[LANGUAGE_NONE][0]['format'] = 'filter_html';
		$node->revision_operation = 1;
		$node->language = 'en';
	}


	/* Implement a prepareRow method to manipulate the source row before saving */
	public function prepareRow($row) {
		return TRUE;
	}

	// Rollback function
	protected function rollback() {
		parent::rollback();
	}
}

?>