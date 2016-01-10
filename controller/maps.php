<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace saturnZ\maps\controller;

use Symfony\Component\HttpFoundation\Response;

class maps
{
   protected $config;
   protected $db;
   protected $auth;
   protected $template;
   protected $user;
   protected $helper;
   protected $phpbb_root_path;
   protected $php_ext;

   public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request, \phpbb\pagination $pagination, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $phpbb_root_path, $php_ext, $table_prefix)
   {
      $this->config = $config;
      $this->request = $request;
      $this->pagination = $pagination;
      $this->db = $db;
      $this->auth = $auth;
      $this->template = $template;
      $this->user = $user;
      $this->helper = $helper;
      $this->phpbb_root_path = $phpbb_root_path;
      $this->php_ext = $php_ext;
      $this->table_prefix = $table_prefix;
      define(__NAMESPACE__ . '\MAP', $this->table_prefix . 'map');
      define(__NAMESPACE__ . '\MAP_REPA', $this->table_prefix . 'map_repa');
      define(__NAMESPACE__ . '\USER_TABLE', $this->table_prefix . 'users');
   }

   public function main()
   {
// Output the page
      $this->template->assign_vars(array(
         'MAPS_PAGE_TITLE'   => $this->config['maps_title'],
      ));

	$userid = $this->user->data['user_id'];
	$groups = $this->config['maps_group'];
	$groups_edit = $this->config['maps_group_edit'];
	$groups_delete = $this->config['maps_group_delete'];
	$placemark_posts = $this->config['maps_Placemark_posts'];
	$maps_posts_forum = $this->config['maps_posts_forum'];
	$senter_maps = $this->config['maps_center'];
	$reputation = $this->config['maps_reputation'];
	$bounds = $this->config['maps_bounds'];

	if ($groups == '')
	{
		$groups = 0;
	}
	if ($groups_edit == '')
	{
		$groups_edit = 0;
	}
	if ($groups_delete == '')
	{
		$groups_delete = 0;
	}

	if ($bounds == '1')
	{
		$bounds_maps = " 
			myMap.setBounds(clusterer.getBounds(), {
				checkZoomRange: true
			});
		";
	}
	else
	{
		$bounds_maps = '';
	}


		$sql = "SELECT *
			FROM " . USER_TABLE . "
				WHERE group_id IN ($groups)
					AND user_id = {$userid}";
		$res = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($res);
			
	if ($this->user->data['group_id'] != $row['group_id'])
	{
		if ($this->user->data['user_id'] != ANONYMOUS)
		{
			trigger_error('NOT_AUTHORISED');
		}
		else
		{
			login_box('', $this->user->lang['LOGIN_EXPLAIN_VIEW_MAPS_PAGE']);
		}
	}
			
	$sql = "SELECT t.id, t.user_id, t.title, t.descr, t.coord, t.repa, t.topic, t.forum, s.username, s.user_type, s.user_colour, s.user_id 
		FROM " . MAP . " AS t LEFT JOIN " . USER_TABLE . " AS s ON (s.user_id = t.user_id)";
	$result = $this->db->sql_query($sql);
	while ($row = $this->db->sql_fetchrow($result)) 
	{
		$id = $row['id'];
		$titles = str_replace(array("\r\n", "\r", "\n"), "<br />", $row['title']);
		$descr = str_replace(array("\r\n", "\r", "\n"), "<br />", $row['descr']);
		$coord = $row['coord'];
		$user_id = $row['user_id'];
		$t_id = $row['topic'];
		$f_id = $row['forum'];
		$repa = $row['repa'];
		$username = get_username_string((($row['user_type'] == USER_IGNORE) ? 'no_profile' : 'full'), $row['user_id'], $row['username'], $row['user_colour']);

		if ($row)
		{
		$url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "f={$f_id}&amp;t={$t_id}");

			if ($t_id <> 0)
			{
			$url = "<br /><a href=".append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "f={$f_id}&amp;t={$t_id}").">".$this->user->lang('MAPS_FORUM_MARK')."</a>";			
			}
			else
			{
			$url = "";
			}

				$sql_delete = "SELECT *
					FROM " . USER_TABLE . "
						WHERE group_id IN ($groups_delete)
							AND user_id = {$userid}";
				$res_delete = $this->db->sql_query($sql_delete);
				$row_delete = $this->db->sql_fetchrow($res_delete);
			
				if ($this->user->data['group_id'] == $row_delete['group_id'])
				{
				$delete = "<hr size=1><form method=\"post\"><p><input type=\"hidden\" name=\"del\" value=\"".$id."\"><input type=\"hidden\" name=\"tid\" value=\"".$t_id."\"><input type=\"submit\" value=\"".$this->user->lang('MAPS_DELELE')."\" name=\"delete\" ></p></form>";
				}
				else
				{
				$delete = "";
				}

		if ($repa < 0)
		{
			$r = "color:red; text-align:center; FONT-WEIGHT: bold; FONT-SIZE:11px";
			$r2 = "".$repa."";
		}
		elseif ($repa == 0)
		{
			$r = "color:blue; text-align:center; FONT-WEIGHT: bold; FONT-SIZE:11px";
			$r2 = "".$repa."";
		}
		else
		{
			$r = "color:green; text-align:center; FONT-WEIGHT: bold; FONT-SIZE:11px";
			$r2 = "+".$repa."";
		}

		if ($reputation == '1')
		{
			$repca = "<hr size=1>"
			."<form method=\"post\" style=\"float:left;\">"
			."<input type=\"hidden\" name=\"id\" value=\"$id\">"
			."Репутация метки: <input type=\"submit\" class=\"up_submit\" name=\"up\" value=\"up\" /></form>"
			."<form method=\"post\" style=\"float:left;\">"
			."<input type=\"hidden\" name=\"id\" value=\"$id\">"
			."<input type=\"text\" name=\"text\" value=\"$r2\" SIZE=\"4\" style=\"$r\">"
			."<input type=\"submit\" class=\"down_submit\" name=\"down\" value=\"down\" /></form><br />";
		}
		else
		{
			$repca = '';
		}

		$this->template->assign_block_vars('row', array(
			'PLACEMARK'         => "
				.add(new ymaps.Placemark([$coord], {
					hintContent: '$titles',
					balloonContentHeader: '<strong><big>$titles</big></strong>',
					balloonContentBody: '<hr size=1>$descr',
					balloonContentFooter: '".$this->user->lang('MAPS_USER_MARK').": $username $url $repca $delete'
				}))
			",
         ));
		}
		else
		{
		$this->template->assign_block_vars('row', array(
			'PLACEMARK'         => "",
         ));
		}
	}

		$sql_edit = "SELECT *
			FROM " . USER_TABLE . "
				WHERE group_id IN ($groups_edit)
					AND user_id = {$userid}";
		$res_edit = $this->db->sql_query($sql_edit);
		$row_edit = $this->db->sql_fetchrow($res_edit);
			
	if ($this->user->data['group_id'] == $row_edit['group_id'])
	{
		$this->template->assign_block_vars('edit', array(
			'EDIT'         => "
			myMap.events.add('click', function (e) {
				if (!myMap.balloon.isOpen()) {
					var coords = e.get('coords');
					myMap.balloon.open(coords, {
						contentHeader:'".$this->user->lang('MAPS_ADD_MARK')."',
						contentBody:'<form method=\"post\"><p>".$this->user->lang('MAPS_TITLE_MARK').":<br /><input type=\"text\" name=\"title\" maxlength=\"255\"></p><p>".$this->user->lang('MAPS_DESCR_MARK').":<br /><textarea rows=\"5\" cols=\"30\" name=\"descriptpoint\"></textarea></p><input name=\"pcoord\" type=\"hidden\" value=\"'+ [coords[0].toPrecision(11),coords[1].toPrecision(11)].join(', ') +'\" ><p><input type=\"submit\" value=\"".$this->user->lang('MAPS_SUBMIT')."\" name=\"but\" ></p></form>'
					});
				}
				else {
					myMap.balloon.close();
				}
			});
",
         ));
	}

	$this->template->assign_block_vars('map', array(
		'HEADER'			=> "
			<script src=\"https://api-maps.yandex.ru/2.1/?lang=ru_RU\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\">
				ymaps.ready(function () {
					var myMap = new ymaps.Map('map', {
						center: [$senter_maps],
						zoom: 10
					}),

				clusterer = new ymaps.Clusterer({
				groupByCoordinates: false,
				clusterDisableClickZoom: true,
				clusterHideIconOnBalloonOpen: false,
				geoObjectHideIconOnBalloonOpen: false
				}),

				geoObjects = new ymaps.GeoObject({
					geometry: {
						type: 'Point',
						coordinates: [$senter_maps]
					}
				});

				clusterer
		",

		'FOOTER'			=> "
			clusterer.options.set({
				gridSize: 80,
				clusterDisableClickZoom: false
			});

			myMap.geoObjects.add(clusterer);
			$bounds_maps
				});
			</script>
		",

		'CONTAINER'			=> "
<div id='map' class='container_map'></div>
<br /><center>Copyright © <a href='http://www.ribak72.ru/community/maps'>Карта рыболовных мест</a></center>
		",
	));

	if(isset($_POST['up']))
	{
		$map_id = request_var('id', '', true);

		if ($this->user->data['user_id'] != ANONYMOUS)
		{

		$sql = "SELECT *
			FROM " . MAP_REPA . "
				WHERE map_id = {$map_id}
					AND user_id = {$userid}";
		$res = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($res);
			
			if ($row > 0)
			{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_UP_DOWN_NO')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
			));
			}
			else
			{
				$insert = $this->db->sql_query("INSERT INTO " . MAP_REPA . " (user_id, map_id) VALUES ('$userid', '$map_id')");
				$update = $this->db->sql_query("UPDATE " . MAP . " SET repa=repa+1 WHERE id='$map_id'");

					if($insert and $update){
						$this->template->assign_block_vars('info', array(
							'TEXT'         => "<center><h3>".$this->user->lang('MAPS_UP_DOWN_YES')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
						));
					} 
					else  
					{
						$this->template->assign_block_vars('info', array(
							'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
						));
					}
			}

		}
		else
		{
			login_box('', $this->user->lang['LOGIN_REPUTATION_PAGE']);
		}

	}

	if(isset($_POST['down']))
	{
		$map_id = request_var('id', '', true);

		if ($this->user->data['user_id'] != ANONYMOUS)
		{

		$sql = "SELECT *
			FROM " . MAP_REPA . "
				WHERE map_id = {$map_id}
					AND user_id = {$userid}";
		$res = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($res);
			
			if ($row > 0)
			{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_UP_DOWN_NO')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
			));
			}
			else
			{
				$insert = $this->db->sql_query("INSERT INTO " . MAP_REPA . " (user_id, map_id) VALUES ('$userid', '$map_id')");
				$update = $this->db->sql_query("UPDATE " . MAP . " SET repa=repa-1 WHERE id='$map_id'");

					if($insert and $update){
						$this->template->assign_block_vars('info', array(
							'TEXT'         => "<center><h3>".$this->user->lang('MAPS_UP_DOWN_YES')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
						));
					} 
					else  
					{
						$this->template->assign_block_vars('info', array(
							'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
						));
					}
			}

		}
		else
		{
			login_box('', $this->user->lang['LOGIN_REPUTATION_PAGE']);
		}

	}

	if(isset($_POST['delete']))
	{
		$del = request_var('del', '', true);
		$tid = request_var('tid', '', true);
		$delete = $this->db->sql_query("DELETE FROM " . MAP . " WHERE id = {$del}");
			if($delete){
				if ($tid <> 0)
				{
				   $sql = "SELECT post_id 
			               FROM ". POSTS_TABLE . " 
			               WHERE topic_id = {$tid}";
				   $result = $this->db->sql_query($sql);
				   $row = $this->db->sql_fetchrow($result);
				   $this->db->sql_freeresult($result);
					   if ($row) 
					   {
					      if (!function_exists('delete_posts'))
					      {
					         include($this->phpbb_root_path . 'includes/functions_admin.' . $this->php_ext);
					      }
					      delete_posts('post_id', $row);
					   }
				}
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_DELELE_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
				));
			} 
			else  
			{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
				));
			}
	}

	if(isset($_POST['but']))
	{
		$pcoord = request_var('pcoord', '', true);
		$descriptpoint = strip_tags(str_replace( "'", '"', html_entity_decode(request_var('descriptpoint', '', true), ENT_QUOTES) ));
		$title = strip_tags(str_replace( "'", '"', html_entity_decode(request_var('title', '', true), ENT_QUOTES) ));
 

		if ($descriptpoint == '') {unset($descriptpoint);}
		if ($title == '') {unset($title);}
		if (isset($title) && isset($descriptpoint))
		{

		$sql = "SELECT * 
			FROM " . MAP . " 
			WHERE coord 
			LIKE '$pcoord'";
		$res = $this->db->sql_query($sql);
		if ($this->db->sql_affectedrows($res) <> 0)
		{
			$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_EXISTS_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
			));
		}
		else
		{
			if ($placemark_posts == 1)
			{
				include($this->phpbb_root_path . '/includes/functions_posting.' . $this->php_ext);
				global $post_data;

				$poll = $uid = $bitfield = $options = ''; 

				generate_text_for_storage($title, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($descriptpoint, $uid, $bitfield, $options, true, true, true);

				$post_data = array(
					'topic_type'				=> POST_NORMAL,
					'post_subject'				=> $title,
				);

				$data = array(
					'forum_id'					=> $maps_posts_forum,
					'icon_id'					=> false,
					'enable_bbcode'				=> (bool) true,
					'enable_smilies'			=> (bool) true,
					'enable_urls'				=> (bool) true,
					'enable_sig'				=> (bool) true,
					'message'					=> $descriptpoint,
					'message_md5'				=> (string) '',
					'bbcode_bitfield'			=> $bitfield,
					'bbcode_uid'				=> $uid,
					'post_edit_locked'			=> 0,
					'topic_title'				=> $title,
					'notify_set'				=> false,
					'notify'					=> false,
					'post_time'					=> 0,
					'forum_name'				=> '',
					'enable_indexing'			=> (bool) false,
					'post_id'					=> '',
					'topic_first_post_id'		=> '',
					'force_approved_state'		=> 1,
				);

				submit_post('post', $title, '', POST_NORMAL, $poll, $data);

$topic = $data['topic_id'];

			}
			else
			{
$topic = 0;
			}

			$insert = $this->db->sql_query("INSERT INTO " . MAP . " (user_id, title, descr, coord, repa, topic, forum) VALUES ('$userid', '$title', '$descriptpoint', '$pcoord', '0', '$topic', '$maps_posts_forum')");
                      
			if($insert){

				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_SAVE_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
				));
			} 
			else  
			{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
				));
			}
		}

		} 
		else  
		{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR2_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"2; url=maps\">",
				));
		}
	}

      page_header($this->config['maps_title']);
      $this->template->set_filenames(array(
         'body' => 'maps_body.html'));

      page_footer();
      return new Response($this->template->return_display('body'), 200); 

	}
}
