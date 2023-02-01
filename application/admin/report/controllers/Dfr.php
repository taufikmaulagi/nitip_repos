<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dfr extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['key_message'] = get_data('key_message', [
			'where' => [
				'produk_grup' => get('pgroup'),
				'is_active' => 1
			]
		])->result_array();
		render($data);
	}
	
}