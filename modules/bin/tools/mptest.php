<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$configs = array(
	'sprice' => array(
		'nopair'  => json_decode('{
			    "plan_a": {
			        "group_id": "3",
			        "prefix": "IJK",
			        "price": "500000",
			        "min_transfer": "500000",
			        "is_withdraw": "1",
			        "surcharge": "6500",
			        "surcharge_npwp": "3%",
			        "surcharge_npwp_no": "10%",
			        "bonus_node": ["5000", "500", "50"],
			        "bonus_gen_node_ok": "1",
			        "bonus_sponsor": ["20000", "6000", "1250"],
			        "flushout_total": "",
			        "flushout_time": "1",
			        "flushout_duration": "DAY",
			        "flushwait_time": "1",
			        "flushwait_duration": "DAY",
			        "bonus_pair": [],
			        "reward_use": "1",
			        "reward_auto": "1",
			        "reward_list": ["Sandal", "Sepatu", "Tas", "Sepeda", "Motor", "Mobil"],
			        "level_list": ["Reguler", "Starter", "Average", "Advance", "Expert"],
			        "serial_use": 0,
			        "serial_check": 0,
			        "serial_flushout_ok": 0,
			        "flushwait": 0,
			        "serial_list": ["Reguler"],
			        "bonus_gen_node": [0, "1000", "100"]
			    },
			    "reward": [],
			    "level": [],
			    "balance": []
			}', 1),
		'yespair' => json_decode('{
			    "plan_a": {
			        "group_id": "3",
			        "prefix": "IJK",
			        "price": "500000",
			        "min_transfer": "500000",
			        "is_withdraw": "1",
			        "surcharge": "6500",
			        "surcharge_npwp": "3%",
			        "surcharge_npwp_no": "10%",
			        "bonus_node": ["5000", "500", "50"],
			        "bonus_gen_node_ok": "1",
			        "bonus_sponsor": ["20000", "6000", "1250"],
			        "flushout_total": "12",
			        "flushout_time": "1",
			        "flushout_duration": "DAY",
			        "flushwait": "1",
			        "flushwait_time": "1",
			        "flushwait_duration": "WEEK",
			        "bonus_pair": ["12000", "2000", "200"],
			        "reward_use": "1",
			        "reward_auto": "1",
			        "reward_list": ["Sandal", "Sepatu", "Tas", "Sepeda", "Motor", "Mobil"],
			        "level_list": ["Reguler", "Starter", "Average", "Advance", "Expert"],
			        "serial_use": 0,
			        "serial_check": 0,
			        "serial_flushout_ok": 0,
			        "serial_list": ["Reguler"],
			        "bonus_gen_node": [0, "1000", "100"]
			    },
			    "reward": [],
			    "level": [],
			    "balance": []
			}', 1)
		),
	'mprice' => array(
		'nopair'  => json_decode('{
			    "plan_a": {
			        "group_id": "3",
			        "prefix": "IJK",
			        "price": "500000",
			        "serial_use": "1",
			        "serial_name": {
			            "1": "Silver",
			            "2": "Gold",
			            "3": "Platinum"
			        },
			        "serial_price": {
			            "1": "500000",
			            "2": "750000",
			            "3": "1000000"
			        },
			        "serial_flushout": {
			            "1": "12",
			            "2": "24",
			            "3": "36"
			        },
			        "min_transfer": "500000",
			        "is_withdraw": "1",
			        "surcharge": "6500",
			        "surcharge_npwp": "3%",
			        "surcharge_npwp_no": "10%",
			        "bonus_node": ["5000", "500", "50"],
			        "bonus_gen_node_ok": "1",
			        "bonus_sponsor": ["20000", "6000", "1250"],
			        "flushout_total": "12",
			        "serial_flushout_ok": "1",
			        "flushout_time": "1",
			        "flushout_duration": "DAY",
			        "flushwait": "1",
			        "flushwait_time": "1",
			        "flushwait_duration": "WEEK",
			        "bonus_pair": [],
			        "reward_use": "1",
			        "reward_auto": "1",
			        "reward_list": ["Sandal", "Sepatu", "Tas", "Sepeda", "Motor", "Mobil"],
			        "level_list": ["Reguler", "Starter", "Average", "Advance", "Expert"],
			        "serial_check": 0,
			        "bonus_gen_node": [0, "1000", "100"]
			    },
			    "reward": [],
			    "level": [],
			    "balance": []
			}', 1),
		'yespair' => array(
			'sflushout' => json_decode('{
				    "plan_a": {
				        "group_id": "3",
				        "prefix": "IJK",
				        "price": "500000",
				        "serial_use": "1",
				        "serial_name": {
				            "1": "Silver",
				            "2": "Gold",
				            "3": "Platinum"
				        },
				        "serial_price": {
				            "1": "500000",
				            "2": "750000",
				            "3": "1000000"
				        },
				        "serial_flushout": {
				            "1": "",
				            "2": "",
				            "3": ""
				        },
				        "min_transfer": "500000",
				        "is_withdraw": "1",
				        "surcharge": "6500",
				        "surcharge_npwp": "3%",
				        "surcharge_npwp_no": "10%",
				        "bonus_node": ["5000", "500", "50"],
				        "bonus_gen_node_ok": "1",
				        "bonus_sponsor": ["20000", "6000", "1250"],
				        "flushout_total": "12",
				        "flushout_time": "1",
				        "flushout_duration": "DAY",
				        "flushwait": "1",
				        "flushwait_time": "1",
				        "flushwait_duration": "WEEK",
				        "bonus_pair": ["12000", "2000", "200"],
				        "reward_use": "1",
				        "reward_auto": "1",
				        "reward_list": ["Sandal", "Sepatu", "Tas", "Sepeda", "Motor", "Mobil"],
				        "level_list": ["Reguler", "Starter", "Average", "Advance", "Expert"],
				        "serial_check": 0,
				        "serial_flushout_ok": 0,
				        "bonus_gen_node": [0, "1000", "100"]
				    },
				    "reward": [],
				    "level": [],
				    "balance": []
				}', 1),
			'mflushout' => json_decode('{
				    "plan_a": {
				        "group_id": "3",
				        "prefix": "IJK",
				        "price": "500000",
				        "serial_use": "1",
				        "serial_name": {
				            "1": "Silver",
				            "2": "Gold",
				            "3": "Platinum"
				        },
				        "serial_price": {
				            "1": "500000",
				            "2": "750000",
				            "3": "1000000"
				        },
				        "serial_flushout": {
				            "1": "12",
				            "2": "24",
				            "3": "36"
				        },
				        "min_transfer": "500000",
				        "is_withdraw": "1",
				        "surcharge": "6500",
				        "surcharge_npwp": "3%",
				        "surcharge_npwp_no": "10%",
				        "bonus_node": ["5000", "500", "50"],
				        "bonus_gen_node_ok": "1",
				        "bonus_sponsor": ["20000", "6000", "1250"],
				        "flushout_total": "12",
				        "serial_flushout_ok": "1",
				        "flushout_time": "1",
				        "flushout_duration": "DAY",
				        "flushwait": "1",
				        "flushwait_time": "1",
				        "flushwait_duration": "WEEK",
				        "bonus_pair": ["12000", "2000", "200"],
				        "reward_use": "1",
				        "reward_auto": "1",
				        "reward_list": ["Sandal", "Sepatu", "Tas", "Sepeda", "Motor", "Mobil"],
				        "level_list": ["Reguler", "Starter", "Average", "Advance", "Expert"],
				        "serial_check": 0,
				        "bonus_gen_node": [0, "1000", "100"]
				    },
				    "reward": [],
				    "level": [],
				    "balance": []
				}', 1)
			)
		),
	'old' => array(
		'plan_a'  => json_decode('{
			    "group_id": "3",
			    "prefix": "BJJ",
			    "price": "95000",
			    "serial_use": "1",
			    "serial_list": ["Reguler", "Premium", "Platinum"],
			    "serial_price": ["95000", "150000", "250000"],
			    "serial_check": "0",
			    "is_withdraw": "0",
			    "min_transfer": "250000",
			    "surcharge": "6500",
			    "surcharge_npwp": "3%25",
			    "surcharge_npwp_no": "10%25",
			    "bonus_node": ["1000", "500", "150", "50"],
			    "bonus_gen_node": ["0", "100", "25"],
			    "bonus_sponsor": ["25000", "12000", "5000"],
			    "bonus_pair": ["10000", "2000", "500"],
			    "flushout_total": "12",
			    "flushout_time": "1",
			    "flushout_duration": "DAY",
			    "flushwait": "1",
			    "flushwait_time": "1",
			    "flushwait_duration": "DAY",
			    "reward_use": "1",
			    "reward_list": ["sendok", "piring", "panji", "teflon", "kompor", "dapur", "rumah"],
			    "reward_auto": "1",
			    "level_list": ["reguler", "starter", "profesional", "advance"]
			}', 1),
		'reward'  => [],
		'level'   => [],
		'balance' => [],
		)
);
$config   = array();
$config[] = array($configs['sprice']['nopair'], 'sprice-nopair');
$config[] = array($configs['sprice']['yespair'], 'sprice-yespair');
$config[] = array($configs['mprice']['nopair'], 'mprice-nopair');
$config[] = array($configs['mprice']['yespair']['sflushout'], 'mprice-yespair-sflushout');
$config[] = array($configs['mprice']['yespair']['mflushout'], 'mprice-yespair-mflushout');
$config[] = array($configs['old'], 'old-config');
$i        = 0;
require_once '/Users/me/Sites/mlm/modules/bin/admin/_function.php';
$sys->stop(false);
$sys->set_layout('blank');
$rows = array();
foreach ($config as $cfg)
{
	$rows[] = array(
		pr($cfg[1]."\n".json_encode($cfg[0], JSON_PRETTY_PRINT), 1),
		pr(json_encode(bin_marketplan_validate($cfg[0]), JSON_PRETTY_PRINT), 1)
		);
}
echo table(
	$rows,
	array(
		'posting',
		'output'
		)
	);
/*

// single price
// 	no pair bonus
// 	yes pair bonus

// multi price
// 	no pair bonus
// 	yes pair bonus
// 		single flushout
// 		multi flushout

// */