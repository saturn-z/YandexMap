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
   'MAPS_USER_MARK'   => 'Автор метки',
   'MAPS_ADD_MARK'   => 'Добавление новой метки!',
   'MAPS_TITLE_MARK'   => 'Название метки',
   'MAPS_DESCR_MARK'   => 'Описание метки',
   'MAPS_SUBMIT'   => 'Добавить',
   'MAPS_SAVE_MARK'   => 'Метка успешно добавлена',
   'MAPS_ERROR_MARK'   => 'Неизвестная ошибка',
   'MAPS_ERROR2_MARK'   => 'Вы ввели не всю информацию, поэтому метка не может быть добавлена!',
   'MAPS_EXISTS_MARK'   => 'Метка с такими координатами уже существует!',
));
