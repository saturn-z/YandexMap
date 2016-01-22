<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace saturnZ\maps\migrations;

class maps_1_0_7 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['maps_version']) && version_compare($this->config['maps_version'], '1.0.7', '>=');
	}

	static public function depends_on()
	{
		return array('\saturnZ\maps\migrations\maps_1_0_6');
	}

	public function update_schema()
	{
		return array();
	}
	public function revert_schema()
	{
		return array();
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'install_bbcode_yandexmaps'))),
			// Current version
			array('config.update', array('maps_version', '1.0.7')),
			// Add configs
			array('config.add', array('maps_post', '1')),
		);
	}

	public function install_bbcode_yandexmaps()
	{
		// Load the acp_bbcode class
		if (!class_exists('acp_bbcodes'))
		{
			include($this->phpbb_root_path . 'includes/acp/acp_bbcodes.' . $this->php_ext);
		}

		$ym_tpl = "<script type=\"text/javascript\">
			map_div_id = (typeof map_div_id == \"undefined\") ? 1 : (map_div_id+1);
			document.writeln('<div id=\"map_' + map_div_id + '\" style=\"width:auto;height:400px;\"></div>');

			(function(){
				var divID = map_div_id;
				ymaps.ready(function(){
						window.myMap = new ymaps.Map('map_' + divID, {
							center: [{INTTEXT}],
							zoom: 10
						});
						var myPlacemark = new ymaps.Placemark([{INTTEXT}]);
						myMap.geoObjects.add(myPlacemark);
				});
			})();
		</script>";

		$ym_tpl_full = "<script type=\"text/javascript\">
			map_div_id = (typeof map_div_id == \"undefined\") ? 1 : (map_div_id+1);
			document.writeln('<div id=\"map_' + map_div_id + '\" style=\"width:auto;height:400px;\"></div>');
			(function(){
				var divID = map_div_id;
				ymaps.ready(function(){
						window.myMap = new ymaps.Map('map_' + divID, {
							center: [{INTTEXT}],
							zoom: 10
						});
						var myPlacemark = new ymaps.Placemark([{INTTEXT}], {hintContent: '{TEXT}'});
						myMap.geoObjects.add(myPlacemark);
				});
			})();
		</script>";

		$bbcode_tool = new \acp_bbcodes();
		$bbcode_data = array(
			'yandexmaps' => array(
				'bbcode_helpline'	=> '{L_YANDEXMAPSHELP}',
				'bbcode_match'		=> '[yandexmaps]{INTTEXT}[/yandexmaps]',
				'bbcode_tpl'		=> $ym_tpl,
				'display_on_posting'=> 0,
			),
			'yandexmaps=' => array(
				'bbcode_helpline'	=> '{L_YANDEXMAPSHELP2}',
				'bbcode_match'		=> '[yandexmaps={TEXT}]{INTTEXT}[/yandexmaps]',
				'bbcode_tpl'		=> $ym_tpl_full,

				'display_on_posting'=> 0,
			),
		);
		foreach ($bbcode_data as $bbcode_name => $bbcode_array)
		{
			// Build the BBCodes
			$data = $bbcode_tool->build_regexp($bbcode_array['bbcode_match'], $bbcode_array['bbcode_tpl']);
			$bbcode_array += array(
				'bbcode_tag'			=> $data['bbcode_tag'],
				'first_pass_match'		=> $data['first_pass_match'],
				'first_pass_replace'	=> $data['first_pass_replace'],
				'second_pass_match'		=> $data['second_pass_match'],
				'second_pass_replace'	=> $data['second_pass_replace']
			);
			$sql = 'SELECT bbcode_id
				FROM ' . $this->table_prefix . "bbcodes
				WHERE LOWER(bbcode_tag) = '" . strtolower($bbcode_name) . "'
				OR LOWER(bbcode_tag) = '" . strtolower($bbcode_array['bbcode_tag']) . "'";
			$result = $this->db->sql_query($sql);
			$row_exists = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($row_exists)
			{
				// Update existing BBCode
				$bbcode_id = $row_exists['bbcode_id'];
				$sql = 'UPDATE ' . $this->table_prefix . 'bbcodes
					SET ' . $this->db->sql_build_array('UPDATE', $bbcode_array) . '
					WHERE bbcode_id = ' . $bbcode_id;
				$this->db->sql_query($sql);
			}
			else
			{
				// Create new BBCode
				$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id
					FROM ' . $this->table_prefix . 'bbcodes';
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				if ($row)
				{
					$bbcode_id = $row['max_bbcode_id'] + 1;
					// Make sure it is greater than the core BBCode ids...
					if ($bbcode_id <= NUM_CORE_BBCODES)
					{
						$bbcode_id = NUM_CORE_BBCODES + 1;
					}
				}
				else
				{
					$bbcode_id = NUM_CORE_BBCODES + 1;
				}
				if ($bbcode_id <= BBCODE_LIMIT)
				{
					$bbcode_array['bbcode_id'] = (int) $bbcode_id;
					$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'bbcodes ' . $this->db->sql_build_array('INSERT', $bbcode_array));
				}
			}
		}
	}


}
