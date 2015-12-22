<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace saturnZ\maps\acp;

class maps_module
{
   var $u_action;

   function main($id, $mode)
   {
      global $db, $user, $auth, $template, $cache, $request;
      global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$this->db = $db;
		$this->config = $config;
		$this->request = $request;
      $this->tpl_name = 'acp_maps';
      $this->page_title = $user->lang('ACP_MAPS');
      add_form_key('saturnZ/maps');

      if ($request->is_set_post('submit'))
      {
         if (!check_form_key('saturnZ/maps'))
         {
            trigger_error('FORM_INVALID');
         }

			$config->set('maps_center', $request->variable('center', '57.16565146, 65.54499550'));
			$config->set('maps_title', $request->variable('title', 'Яндекс Карта',true));
			$config->set('maps_group', implode(',', $request->variable('maps_group', array(0))));
			$config->set('maps_group_edit', implode(',', $request->variable('maps_group_edit', array(0))));
			$config->set('maps_posts_forum', $request->variable('maps_posts_forum', 1));
			$config->set('maps_Placemark_posts', $request->variable('Placemark', 0));

         trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
      }

		$groups_ary = explode(',', $this->config['maps_group']);
		$groups_edit_ary = explode(',', $this->config['maps_group_edit']);
		$groups = $this->config['maps_group'];
		if ($groups == '')
		{
			$groups = 0;
		}
		// get group info from database and assign the block vars
		$sql = 'SELECT group_id, group_name 
				FROM ' . GROUPS_TABLE . '
				ORDER BY group_id ASC';
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$template->assign_block_vars('group_setting', array(
				'SELECTED'		=> (in_array($row['group_id'], $groups_ary)) ? true : false,
				'GROUP_NAME'	=> (isset($user->lang['G_' . $row['group_name']])) ? $user->lang['G_' . $row['group_name']] : $row['group_name'],
				'GROUP_ID'		=> $row['group_id'],
			));
		}

		$sql_edit = "SELECT group_id, group_name 
				FROM " . GROUPS_TABLE . "
				WHERE group_id IN ($groups)
				ORDER BY group_id ASC";
		$res = $this->db->sql_query($sql_edit);
		while($row = $this->db->sql_fetchrow($res))
		{
			$template->assign_block_vars('group_edit_setting', array(
				'SELECTED'		=> (in_array($row['group_id'], $groups_edit_ary)) ? true : false,
				'GROUP_NAME'	=> (isset($user->lang['G_' . $row['group_name']])) ? $user->lang['G_' . $row['group_name']] : $row['group_name'],
				'GROUP_ID'		=> $row['group_id'],
			));
		}

      $template->assign_vars(array(
		'U_ACTION'      => $this->u_action,
		'CENTER'      => (isset($config['maps_center'])) ? $config['maps_center'] : '57.16565146, 65.54499550',
		'TITLE'      => (isset($config['maps_title'])) ? $config['maps_title'] : 'Яндекс Карта',
		'POSTS_FORUM'      => make_forum_select(((isset($config['maps_posts_forum'])) ? $config['maps_posts_forum'] : 1), false, false, true),
		'PLACEMARK'		=> (isset($this->config['maps_Placemark_posts'])) ? $this->config['maps_Placemark_posts'] : 0,
      ));
   }
}
