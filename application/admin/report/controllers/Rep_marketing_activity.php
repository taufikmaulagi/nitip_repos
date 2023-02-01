<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rep_marketing_activity extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['matrix'] = get_data('rumus_customer_matrix', [
			'where' => [
				'produk_grup' => get('pgroup'),
				'is_active' => 1
			],
			'group_by' => 'matrix',
			'sort_by' => 'matrix',
			'sort' => 'ASC'
		])->result_array();
		render($data);
	}
}