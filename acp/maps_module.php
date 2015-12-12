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

         trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
      }

      $template->assign_vars(array(
         'U_ACTION'      => $this->u_action,
         'CENTER'      => (isset($config['maps_center'])) ? $config['maps_center'] : '57.16565146, 65.54499550',
         'TITLE'      => (isset($config['maps_title'])) ? $config['maps_title'] : 'Яндекс Карта',
      ));
   }
}
