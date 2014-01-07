<?php

/**
 * add_moon.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$xnova_root_path = './../';
include($xnova_root_path . 'extension.inc');
include($xnova_root_path . 'common.' . $phpEx);

	if ($user['authlevel'] >= 2) {
		includeLang('admin');
		$parse     = $lang;

		$mode      = $_POST['mode'];

		$PageTpl   = gettemplate("admin/add_moon");

		if ($mode == 'addit') {
			$PlanetID  = $_POST['user'];
			$MoonName  = $_POST['name'];

			$QrySelectPlanet  = "SELECT * FROM {{table}} ";
			$QrySelectPlanet .= "WHERE ";
			$QrySelectPlanet .= "`id` = '". $PlanetID ."';";
			$PlanetSelected = doquery ( $QrySelectPlanet, 'planets', true);

			$Galaxy    = $PlanetSelected['galaxy'];
			$System    = $PlanetSelected['system'];
			$Planet    = $PlanetSelected['planet'];
            $Owner     = $PlanetSelected['id_owner'];
			$MoonID    = time();

			CreateOneMoonRecord ( $Galaxy, $System, $Planet, $Owner, $MoonID, $MoonName, 20 );

			AdminMessage ( $lang['addm_done'], $lang['addm_title'] );
		}
		$Page = parsetemplate($PageTpl, $parse);

		display ($Page, $lang['addm_title'], false, '', true);
	} else {
		AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
?>