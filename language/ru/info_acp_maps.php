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
   'ACP_MAPS'            => 'Яндекс карта',
   'ACP_MAPS_EXPLAIN'      => 'Здесь можно настроить параметры расширения.',
   'ACP_MAPS_CENTER'      => 'Координаты центра карты',
   'ACP_MAPS_CENTER_INFO'      => 'В формате: xx.xxxxxxxx, xx.xxxxxxxx<br />Определить координаты можно <a href=http://dimik.github.io/ymaps/examples/location-tool/>ЗДЕСЬ</a>',
   'ACP_MAPS_TITLE'      => 'Название карты',
   'ACP_MAPS_TITLE_INFO'      => 'Заголовок страницы расширения (page_title)',
   'ACP_MAPS_SETTINGS'      => 'Настройки',
   'ACP_MAPS_SAVE'      => 'Сохранить изменения.',
));