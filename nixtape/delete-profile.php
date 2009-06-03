<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once ('templating.php');
require_once ('data/User.php');
require_once ('utils/random_code_generator.php');
require_once($install_path . '/../turtle/temp-utils.php'); // this is extremely dodgy and shameful

if ($logged_in == false) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die ();
} elseif ( isset ($_GET['code'])) {
	$adodb->Execute('DELETE FROM Delete_Request WHERE expires < ' . (int)(time()));

	$username = $this_user->name;
	$code = $_GET['code'];
try {
	$res = $adodb->GetRow('SELECT * FROM Delete_Request WHERE username = ' . $adodb->qstr($username) . ' AND code = ' . $adodb->qstr($code));
}
catch (exception $e) {
		exit;
	}
	if (!$res) {
		$error = 'Invalid code.';
		$smarty->assign('error', 'Error!');
		$smarty->assign('details', $error);
		$smarty->display('error.tpl');
		die ();
	} else {
		$adodb->Execute('DELETE FROM Scrobble_Sessions WHERE userid = ' . $adodb->qstr($this_user->uniqueid));
		$adodb->Execute('DELETE FROM Delete_Request WHERE username = ' . $adodb->qstr($username));
		$adodb->Execute('DELETE FROM Auth WHERE username = ' . $adodb->qstr($username));
		$adodb->Execute('DELETE FROM Group_Members WHERE member = ' . (int)($this_user->uniqueid));
		$adodb->Execute('DELETE FROM Radio_Sessions WHERE username = ' . $adodb->qstr($username));
		$adodb->Execute('DELETE FROM Recovery_Request WHERE username = ' . $adodb->qstr($username));
		$adodb->Execute('DELETE FROM Scrobbles WHERE userid = ' . username_to_uniqueid($username));
		$adodb->Execute('DELETE FROM User_Relationship_Flags WHERE uid1 = ' . (int)($this_user->uniqueid));
		$adodb->Execute('DELETE FROM User_Relationship_Flags WHERE uid2 = ' . (int)($this_user->uniqueid));
		$adodb->Execute('DELETE FROM User_Relationships WHERE uid1 = ' . (int)($this_user->uniqueid));
		$adodb->Execute('DELETE FROM User_Relationships WHERE uid2 = ' . (int)($this_user->uniqueid));
		$adodb->Execute('DELETE FROM Users WHERE lower(username) = ' . $adodb->qstr(strtolower($username)));
		session_destroy();
		header('Location: index.php');
	}
} else {
	$code = generateCode();
	$username = $this_user->name;
	$email = $this_user->email;
	$expire = time()+86400;
	$adodb->Execute('INSERT INTO Delete_Request (code, expires, username) VALUES (' . $adodb->qstr($code) . ', ' . $adodb->qstr($expire) . "," .  $adodb->qstr($username) . ')');
	$url = $base_url . '/delete-profile.php?code=' . $code;
	$content = "Hi!\n\nSomeone from the IP address " . $_SERVER['REMOTE_ADDR'] . " requested account deletion at libre.fm.  To remove this account click: \n\n" . $url . "\n\n- The Libre.fm Team";
	$headers = 'From: Libre.fm <account@libre.fm>';
	$subject = 'Libre.fm Account Delete Request - Action needed!';
	mail($email, $subject, $content, $headers);
	$smarty->display('delete-profile.tpl');
}
?>
