  <?php

  /**

   * @file

   * Article migration.

   */

class ArticleMigration extends XMLMigration {

	public function __construct() {

		parent::__construct();

		$this->description = t('XML feed of Articles.');

		$fields = array('id' => t('ID'),
			'lang_type' => t('Language'),
			'type' => t('Type'),
			'title' => t('Title'),
			'teaser' => t('Teaser'),
			'article_body' => t('Article Body'),
		);

		$this->map = new MigrateSQLMap(
			$this->machineName,
			array('ID' => 
				array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'Source ID',
			 	)
			), 
			MigrateDestinationNode::getKeySchema()
		);

	    $list_url = 'http://www.vision.org/visionmedia/generateexportlist.aspx';
	    $item_url = 'http://www.vision.org/visionmedia/generatecontentXML.aspx?id=:id';

		$this->source = new MigrateSourceList(
								new MigrateListXML($list_url),
								new MigrateItemXML($item_url), $fields
							);
    	$this->destination = new MigrateDestinationNode('article');

    	$this->addFieldMapping('body', 'article_body')->arguments(array('format' => 'filtered_html'))->xpath('/content/html/root/article/Body');
    	$this->addFieldMapping('body:summary', 'teaser')->arguments(array('format' => 'filtered_html'))->xpath('/content/Teaser');
    	$this->addFieldMapping('title', 'title')->xpath('/content/Title');
	}

  /**

   * {@inheritdoc}

   */

    public function prepareRow($row) {

	    if (parent::prepareRow($row) === FALSE) {
	      return FALSE;
	    }

	    $ctype = (string)$row->XML->Type;

		if((string)$row->XML->root->article->Title == ''){
	   		$row->XML->root->article->Title = $row->XML->root->Title;
	    }

        $postscript = $row->XML->html->root->article->Postscript->asXML();
    	$postscript = str_replace('','',$postscript);
    	$postscript = str_replace('','',$postscript);

    	$row->XML->html->root->article->Postscript = $postscript;

    	$body = $row->XML->html->root->article->Body->asXML();
    	$body = str_replace('','',$body);
    	$body = str_replace('','',$body);

    	$row->XML->html->root->article->Body = $body;

	    $title = $row->XML->html->root->article->Title->asXML();
	    $title = str_replace('','',$title);
	    $title = str_replace('','',$title);

    	return TRUE;
  	}

  	function vision_migrate_migrate_api() {

	  $api = array(
	    'api' => 2,
	    'groups' => array(
	      'article' => array(
	        'title' => t('Vision'),
	      ),
	    ),
	    'migrations' => array(
	      'Article' => array('class_name' => 'ArticleMigration'),
	    ),
	  );

	  return $api;
	}

}