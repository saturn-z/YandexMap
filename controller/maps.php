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
	$placemark_posts = $this->config['maps_Placemark_posts'];
	$maps_posts_forum = $this->config['maps_posts_forum'];

	if ($groups == '')
	{
		$groups = 0;
	}
	if ($groups_edit == '')
	{
		$groups_edit = 0;
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
			
	$senter_maps = $this->config['maps_center'];

	$sql = "SELECT t.user_id, t.title, t.descr, t.coord, t.topic, t.forum, s.username, s.user_type, s.user_colour, s.user_id 
		FROM " . MAP . " AS t LEFT JOIN " . USER_TABLE . " AS s ON (s.user_id = t.user_id)";
	$result = $this->db->sql_query($sql);
	while ($row = $this->db->sql_fetchrow($result)) 
	{
		$titles = $row['title'];
		$descr = $row['descr'];
		$coord = $row['coord'];
		$user_id = $row['user_id'];
		$t_id = $row['topic'];
		$f_id = $row['forum'];
		$username = get_username_string((($row['user_type'] == USER_IGNORE) ? 'no_profile' : 'full'), $row['user_id'], $row['username'], $row['user_colour']);

		if ($row)
		{
		$user_id = $row['user_id'];
		$titles = $row['title'];
		$descr = $row['descr'];
		$coord = $row['coord'];
		$t_id = $row['topic'];
		$f_id = $row['forum'];
		$url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "f={$f_id}&amp;t={$t_id}");

			if ($t_id <> 0)
			{
			$url = "<br /><a href=".append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "f={$f_id}&amp;t={$t_id}").">".$this->user->lang('MAPS_FORUM_MARK')."</a>";			
			}
			else
			{
			$url = "";
			}

		$this->template->assign_block_vars('row', array(
			'PLACEMARK'         => "
				.add(new ymaps.Placemark([$coord], {
					hintContent: '$titles',
					balloonContentHeader: '<strong><big>$titles</big></strong>',
					balloonContentBody: '<hr size=1>$descr',
					balloonContentFooter: '".$this->user->lang('MAPS_USER_MARK').": $username $url'
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
/*
//			myMap.setBounds(clusterer.getBounds(), {
//				checkZoomRange: true
//			});
*/
				});
			</script>
		",
	));

	$userid = $this->user->data['user_id'];
	if(isset($_POST['but']))
	{
		$pcoord = request_var('pcoord', '', true);
		$descriptpoint = request_var('descriptpoint', '', true);
		$title = request_var('title', '', true);

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
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_EXISTS_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"3; url=maps\">",
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
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_SAVE_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"3; url=maps\">",
				));
			} 
			else  
			{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"3; url=maps\">",
				));
			}
		}

		} 
		else  
		{
				$this->template->assign_block_vars('info', array(
					'TEXT'         => "<center><h3>".$this->user->lang('MAPS_ERROR2_MARK')."</h3></center><meta http-equiv=\"refresh\" content=\"3; url=maps\">",
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
