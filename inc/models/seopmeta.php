<?php
namespace WeDevs\ERP\Framework\Models;

use WeDevs\ERP\Framework\Model;

class SeoMeta extends \WeDevs\ORM\Eloquent\Model {

	protected $primaryKey = 'object_id';
	protected $table      = 'yoast_seo_meta';
	public $timestamps    = false;
}
