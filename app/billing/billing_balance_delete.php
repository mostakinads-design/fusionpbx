<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2025
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('billing_balance_delete')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the id
	if (is_array($_GET) && @sizeof($_GET) != 0) {
		$id = $_GET["id"];
	}

//validate the token
	$token = new token;
	if (!$token->validate($_SERVER['PHP_SELF'])) {
		message::add($text['message-invalid_token'],'negative');
		header('Location: billing_balances.php');
		exit;
	}

//delete the data
	if (is_uuid($id)) {
		//build the delete array
		$array['billing_balances'][0]['billing_balance_uuid'] = $id;
		$array['billing_balances'][0]['domain_uuid'] = $_SESSION['domain_uuid'];

		//execute delete
		$database = new database;
		$database->app_name = 'billing';
		$database->app_uuid = 'b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f';
		$database->delete($array);
		unset($array);

		message::add($text['message-delete']);
	}

//redirect the user
	header('Location: billing_balances.php');
	exit;

?>
