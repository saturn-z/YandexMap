<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace saturnZ\maps\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
      return array(
         'core.user_setup'                  => 'load_language_on_setup',
         'core.page_header'                  => 'add_page_header_link',
      );
	}

	/**
	* Constructor
	*/
	public function __construct($phpbb_root_path, \phpbb\template\template $template)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->template = $template;
	}

   public function add_page_header_link($event)
   {
      $this->template->assign_vars(array(
         'U_MAPS' => append_sid("{$this->phpbb_root_path}maps"),
      ));
   }

   public function load_language_on_setup($event)
   {
      $lang_set_ext = $event['lang_set_ext'];
      $lang_set_ext[] = array(
         'ext_name' => 'saturnZ/maps',
         'lang_set' => 'maps_lng',
      );
      $event['lang_set_ext'] = $lang_set_ext;
   }
}
