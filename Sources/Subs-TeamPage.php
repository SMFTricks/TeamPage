<?php

/**
 * Subs-TeamPage
 *
 * @package Team Page
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

class TeamPage
{

	public static $name = 'TeamPage';

	/**
	 * TeamPage::permissions()
	 *
	 * There is only permissions to post new status and comments on any profile because people needs to be able to post in their own profiles by default the same goes for deleting, people are able to delete their own status/comments on their own profile page.
	 * @param array $permissionList An associative array with all the possible permissions.
	 * @return void
	 */
	public static function permissions(&$permissionGroups, &$permissionList)
	{
		$permissionList['membergroup']['view_teampage'] = array(false, 'general', 'view_basic_info');
	}


	/**
	 * TeamPage::menu()
	 *
	 * Insert a Team Page button on the menu buttons array
	 * @param array $menu_buttons An array containing all possible tabs for the main menu.
	 * @return void
	 */
	public static function menu(&$menu_buttons)
	{
		global $context, $txt, $scripturl, $modSettings;
		
		// Bring the css files
		self::loadcss();

		// The TeamPage link
		$insert = 'home'; // for now lets use the home button as reference...
		$counter = 3;

		foreach ($menu_buttons as $area => $dummy)
			if (++$counter && $area == $insert )
				break;

		$menu_buttons = array_merge(
			array_slice($menu_buttons, 0, $counter),
			array('teampage' => array(
				'title' => self::text('main_button'),
				'href' => $scripturl . '?action=teampage',
				'show' => allowedTo('view_teampage') && !empty($modSettings['TeamPage_enable']),
				),
			),
			array_slice($menu_buttons, $counter)
		);

		// DUH! winning!
		TeamPage::who();
	}
	
	/**
	 * TeamPage::loadcss()
	 *
	 * Used in the teampage action for styling everything.
	 * @param it will load our CSS for using it in the teampage action
	 * @return
	 */
	public static function loadcss()
	{
		global $settings, $context;
		
		$context['html_headers'] .= '
			<link href="'. $settings['default_theme_url'] .'/css/TeamPage.css" rel="stylesheet" type="text/css" />';
		
	}

	/**
	 * TeamPage::who()
	 *
	 * Used in the credits action.
	 * @param boolean $return decide between returning a string or append it to a known context var.
	 * @return string a link for copyright notice
	 */
	public static function who($return = false)
	{
		global $context, $txt;

		// Show this only in pages generated by Team Page.
		if (!$return && isset($context['current_action']) && $context['current_action'] === 'credits')
			$context['copyrights']['mods'][] = '<a href="http://smftricks.com" title="SMF Themes & Mods">Team Page mod &copy Diego Andr&eacute;s</a>';
			
	}
	
	/**
	 * TeamPage::online()
	 *
	 * Used in the who's online action.
	 * @param $who is going to return the text for telling them what are they doing.
	 * @return string for the current action
	 */
	public static function online($actions)
	{
		global $context, $txt;

		// Show this only in the who's online action.
		if (isset($actions['action']) && ($actions['action'] === 'teampage'))
			return self::text('whoall_teampage');
			
	}

	/**
	 * TeamPage::admin()
	 *
	 * Adding the admin section
	 * @param array $admin_menu An array with all the admin settings buttons
	 * @return
	 */
	public static function admin(&$admin_menu)
	{
		global $context, $txt, $scripturl, $sourcedir;

		include_once($sourcedir. '/TeamPageAdmin.php');
		
		$admin_menu['config']['areas']['teampage'] = array(
			'label' => self::text('main_button'),
			'file' => 'Subs-TeamPage.php',
			'function' => 'loadAdmin',
			'icon' => 'server.gif',
			'subsections' => array(
				'settings' => array(self::text('page_settings')),
				'pages' => array(self::text('page_pages')),
			),
		);
	}

	/**
	 * TeamPage::actions()
	 *
	 * Insert the actions needed by this mod
	 * @param array $actions An array containing all possible SMF actions.
	 * @return void
	 */
	public static function actions(&$actions)
	{
		// The magister action
		$actions['teampage'] = array('TeamPage.php', 'TeamPage');
	}

	/**
	 * TeamPageTools::text()
	 *
	 * Gets a string key, and returns the associated text string.
	 * @param string $var The text string key.
	 * @global $txt
	 * @return string|boolean
	 * @copyright Jessica González <suki@missallsunday.com>
	 */
	public static function text($var)
	{
		global $txt;

		if (empty($var))
			return false;

		// Load the mod's language file.
		loadLanguage(self::$name);

		if (!empty($txt[self::$name. '_' .$var]))
			return $txt[self::$name. '_' .$var];

		else
			return false;
	}
}