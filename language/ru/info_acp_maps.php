<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
   exit;
}

if (empty($lang) || !is_array($lang))
{
   $lang = array();
}

$lang = array_merge($lang, array(
	'ACP_MAPS'							=> 'Яндекс карта',
	'ACP_MAPS_EXPLAIN'					=> 'Здесь можно настроить параметры расширения.',
	'ACP_MAPS_CENTER'					=> 'Координаты центра карты',
	'ACP_MAPS_CENTER_INFO'				=> 'В формате: xx.xxxxxxxx, xx.xxxxxxxx<br />Определить координаты можно <a href=http://dimik.github.io/ymaps/examples/location-tool/>ЗДЕСЬ</a>',
	'ACP_MAPS_TITLE'					=> 'Название карты',
	'ACP_MAPS_TITLE_INFO'				=> 'Заголовок страницы расширения (page_title)',
	'ACP_MAPS_SETTINGS'      			=> 'Настройки',
	'ACP_MAPS_SAVE'      				=> 'Сохранить изменения.',
	'ACP_MAPS_GROUP'					=> 'Кто будет видеть карту',
	'ACP_MAPS_GROUP_INFO'				=> 'Выберите группы, которые будут иметь доступ к карте.',
	'ACP_MAPS_CTRL'						=> 'Для множественного выбора удерживайте клавишу <strong>CTRL</strong>.',
	'ACP_MAPS_GROUP_EDIT'				=> 'Кто может ставить метки',
	'ACP_MAPS_GROUP_EDIT_INFO'			=> 'Выберите группы, которые будут иметь возможность ставить новые метки на карте.',
	'ACP_MAPS_PLACEMARK_POSTS'			=> 'Создавать темы к меткам на форуме',
	'ACP_MAPS_POSTS_FORUM'				=> 'В каком форуме создавать темы',
	'ACP_MAPS_POSTS_FORUM_INFO'			=> 'Выберите форум, в котором будут создаваться темы при создании новой метки.',
));
