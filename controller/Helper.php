<?php


use \Tuanduimao\Loader\App as App;
use \Tuanduimao\Utils as Utils;
use \Tuanduimao\Tuan as Tuan;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Conf as Conf;


class HelperController extends \Tuanduimao\Loader\Scaffold {
	
	function __construct() {
		$md = (isset($_GET['model_name']) && !empty($_GET['model_name']))? trim($_GET['model_name']) : 'datatype';
		parent::__construct("$md");
	}
	
/*
	function demodata() {

		$csr = App::M('Customer');

		try {
			$csr->dropTable();
		} catch( Excp $e ) {}

		try {
			App::M('Customer')->__schema();
		} catch( Excp $e ) {}

		// 生成随机数据
		$faker = Utils::faker();
		for ( $i=0; $i<200; $i++ ) {
			try {
				$cust = $csr->create([
					'company'=> $faker->company,
					'name'=> $faker->name,
					'title'=> $faker->jobTitle,
					'mobile'=> $faker->phoneNumber,
					'email'=> $faker->email,
					'address'=> $faker->address,
					'remark'=> $faker->text(100),
					'status'=>'active'
				]);
			} catch( Excp $e ) {}
		}

		// 生成签到演示数据

		echo json_encode(["code"=>0, "message"=>"SUCCESS"]);
	}
*/

	function tmp_field_general() {
		$json_text = file_get_contents(App::$APP_ROOT . "/config/field-server-sample.json");

		$json_data = json_decode( $json_text , true );
		foreach ($json_data['fields'] as $idx => $field) {

			if ( !isset( $field['uuid'] ) ) {
				$uuidHash = $field;
				unset( $uuidHash['host'] );
				unset($uuidHash['default']);

				$uuidHash['option'] = (isset($uuidHash['option']) && is_array($uuidHash['option'])) ? $uuidHash['option'] : [];
				$uuidHash['option_mobile'] = (isset($uuidHash['option_mobile']) && is_array($uuidHash['option_mobile'])) ? $uuidHash['option_mobile'] : [];


				foreach ($uuidHash['option'] as $idxo=> $opt) {
					ksort($uuidHash['option'][$idxo]);
				}

				foreach ($uuidHash['option_mobile'] as $idxo=> $opt) {
					ksort($uuidHash['option_mobile'][$idxo]);
				}


				ksort( $uuidHash['option'] );
				ksort( $uuidHash['option_mobile'] );
				ksort($uuidHash);
				$uuid = md5(json_encode($uuidHash));

				$json_data['fields'][$idx]['uuid'] = $uuid;
			}
		}


		header('Content-Type: application/json');
		echo json_encode($json_data);
	}

	function tmp_field_estate() {
		$json_text = file_get_contents(App::$APP_ROOT . "/config/field-server-sample.json");
		$json_data = json_decode($json_text, true);
		$json_data['server']['name'] = '地产经纪字段库';
		$json_data['server']['host'] = 'http://pt.tuanduimao.com/service/field/estate';

		foreach ($json_data['fields'] as $idx => $field) {

			if ( !isset( $field['uuid'] ) ) {
				$uuidHash = $field;
				unset( $uuidHash['host'] );
				unset($uuidHash['default']);
				
				$uuidHash['option'] = (isset($uuidHash['option']) && is_array($uuidHash['option'])) ? $uuidHash['option'] : [];
				$uuidHash['option_mobile'] = (isset($uuidHash['option_mobile']) && is_array($uuidHash['option_mobile'])) ? $uuidHash['option_mobile'] : [];

				foreach ($uuidHash['option'] as $idxo=> $opt) {
					ksort($uuidHash['option'][$idxo]);
				}

				foreach ($uuidHash['option_mobile'] as $idxo=> $opt) {
					ksort($uuidHash['option_mobile'][$idxo]);
				}


				ksort( $uuidHash['option'] );
				ksort( $uuidHash['option_mobile'] );
				ksort($uuidHash);
				$uuid = md5(json_encode($uuidHash));
				$json_data['fields'][$idx]['uuid'] = $uuid;
			}
		}
		header('Content-Type: application/json');
		echo json_encode($json_data);
	}
}