<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace saturnZ\maps\migrations;

class maps_1_0_6 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['maps_version']) && version_compare($this->config['maps_version'], '1.0.6', '>=');
	}

	static public function depends_on()
	{
		return array('\saturnZ\maps\migrations\maps_1_0_5');
	}

    public function update_schema()
    {
        return array(
            'change_columns'    => array(
                $this->table_prefix . 'map'        => array(
                    'repa'                        => array('INT:11', 0),
                ),
            ),
        );
    }

	public function update_data()
	{
		return array(
			// Current version
			array('config.update', array('maps_version', '1.0.6')),
		);
	}


}
