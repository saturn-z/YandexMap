<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace saturnZ\maps\acp;

class maps_info
{
   function module()
   {
      return array(
         'filename'   => '\saturnZ\maps\acp\maps_module',
         'title'      => 'ACP_MAPS',
         'version'   => '1.0.0',
         'modes'      => array(
            'settings'   => array('title' => 'ACP_MAPS_SETTINGS', 'auth' => 'ext_saturnZ/maps && acl_a_board', 'cat' => array('ACP_MAPS')),
         ),
      );
   }
}
