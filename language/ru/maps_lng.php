<?php
/**
*
* @package phpBB Extension - maps
* @copyright (c) 2015 saturn-z
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	'MAPS_TITLE'						=> 'Карта',
	'MAPS_USER_MARK'					=> 'Автор метки',
	'MAPS_ADD_MARK'						=> 'Добавление новой метки!',
	'MAPS_TITLE_MARK'					=> 'Название метки',
	'MAPS_DESCR_MARK'					=> 'Описание метки',
	'MAPS_SUBMIT'						=> 'Добавить',
	'MAPS_SAVE_MARK'					=> 'Метка успешно добавлена',
	'MAPS_DELELE'						=> 'Удалить метку',
	'MAPS_DELELE_MARK'					=> 'Метка успешно удалена',
	'MAPS_ERROR_MARK'					=> 'Неизвестная ошибка',
	'MAPS_ERROR2_MARK'					=> 'Вы ввели не всю информацию, поэтому метка не может быть добавлена!',
	'MAPS_EXISTS_MARK'					=> 'Метка с такими координатами уже существует!',
	'MAPS_FORUM_MARK'					=> 'Обсудить на форуме',
	'LOGIN_EXPLAIN_VIEW_MAPS_PAGE'		=> 'Вы должны быть авторизованы для просмотра этой страницы.',
	'LOGIN_REPUTATION_PAGE'				=> 'Вы должны быть авторизованы для участия в голосовании.',
	'MAPS_UP_DOWN_YES'					=> 'Ваш голос принят! Спасибо за участие в голосовании.',
	'MAPS_UP_DOWN_NO'					=> 'Вы уже голосовали за эту метку.',
	'YANDEXMAPSHELP'					=> '[yandexmaps]координаты[/yandexmaps]',
	'YANDEXMAPSHELP2'					=> '[yandexmaps=заголовок]координаты[/yandexmaps]',
	'MAPS_CLICK_COORD'					=> 'Координаты',
));
