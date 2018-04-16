<?php
class OperatingModel extends RelationModel {
	//关联查询
	protected $_link = array(
		'User' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'User',
			'foreign_key'=>'Uid',
			'mapping_name'=>'user',
			'mapping_fields'=>'Username',
			'as_fields'=>'Username'
		),
	);
}
?>