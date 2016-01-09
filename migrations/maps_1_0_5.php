<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace saturnZ\maps\migrations;

class maps_1_0_5 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['maps_version']) && version_compare($this->config['maps_version'], '1.0.5', '>=');
	}

	static public function depends_on()
	{
		return array('\saturnZ\maps\migrations\maps_1_0_4');
	}

    public function update_schema()
    {
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'map_repa'	=> array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'user_id'		=> array('UINT:11', 0),
						'map_id'		=> array('UINT:11', 0),
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
				$this->table_prefix . 'map_repa',
			),
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.update', array('maps_version', '1.0.5')),

			// Add configs
			array('config.add', array('maps_reputation', '1')),
			array('config.add', array('maps_bounds', '1')),

		);
	}


}
