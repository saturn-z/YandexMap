<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace saturnZ\maps\migrations;

class maps_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'map'	=> array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'user_id'		=> array('UINT:11', 0),
						'title'			=> array('VCHAR:255', ''),
						'descr'			=> array('TEXT', ''),
						'coord'			=> array('VCHAR:30', ''),
						'repa'			=> array('UINT:11', 0),
						'topic'			=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'map',
			),
		);
	}

	public function update_data()
	{
		return array(

			// Current version
			array('config.add', array('maps_version', '1.0.1')),

			// Add configs
			array('config.add', array('maps_title', 'Яндекс Карта')),
			array('config.add', array('maps_center', '57.16565146, 65.54499550')),

			// Add ACP modules
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_MAPS')),
			array('module.add', array('acp', 'ACP_MAPS', array(
				'module_basename'	=> '\saturnZ\maps\acp\maps_module',
				'module_langname'	=> 'ACP_MAPS_SETTINGS',
				'module_mode'		=> 'maps',
				'module_auth'		=> 'ext_saturnZ/maps && acl_a_board', 
			))),
		);
	}
}
