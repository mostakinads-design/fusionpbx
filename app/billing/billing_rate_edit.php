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
	if (permission_exists('billing_rate_add') || permission_exists('billing_rate_edit')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//set the action as an add or an update
	if (is_uuid($_REQUEST["id"])) {
		$action = "update";
		$billing_rate_uuid = $_REQUEST["id"];
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (is_array($_POST) && @sizeof($_POST) != 0) {
		$rate_name = $_POST["rate_name"];
		$rate_description = $_POST["rate_description"];
		$destination_prefix = $_POST["destination_prefix"];
		$rate_per_minute = $_POST["rate_per_minute"];
		$billing_increment = $_POST["billing_increment"];
		$minimum_duration = $_POST["minimum_duration"];
		$connection_fee = $_POST["connection_fee"];
		$currency = $_POST["currency"];
		$enabled = $_POST["enabled"];
	}

//process the http post
	if (is_array($_POST) && @sizeof($_POST) != 0 && $_POST["persistformvar"] != "true") {

		//validate the token
		$token = new token;
		if (!$token->validate($_SERVER['PHP_SELF'])) {
			message::add($text['message-invalid_token'],'negative');
			header('Location: billing_rates.php');
			exit;
		}

		//check for required data
		$msg = '';
		if (strlen($rate_name) == 0) { $msg .= $text['message-required'] . " " . $text['label-rate_name'] . "<br>\n"; }
		if (strlen($destination_prefix) == 0) { $msg .= $text['message-required'] . " " . $text['label-destination_prefix'] . "<br>\n"; }
		if (strlen($rate_per_minute) == 0) { $msg .= $text['message-required'] . " " . $text['label-rate_per_minute'] . "<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persist_form_var.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg . "<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

		//add or update the database
		if ($_POST["persistformvar"] != "true") {
			//build the array
			$array['billing_rates'][0]['domain_uuid'] = $_SESSION['domain_uuid'];
			if ($action == "add" && permission_exists('billing_rate_add')) {
				$billing_rate_uuid = uuid();
				$array['billing_rates'][0]['billing_rate_uuid'] = $billing_rate_uuid;
			}
			$array['billing_rates'][0]['rate_name'] = $rate_name;
			$array['billing_rates'][0]['rate_description'] = $rate_description;
			$array['billing_rates'][0]['destination_prefix'] = $destination_prefix;
			$array['billing_rates'][0]['rate_per_minute'] = $rate_per_minute;
			$array['billing_rates'][0]['billing_increment'] = $billing_increment;
			$array['billing_rates'][0]['minimum_duration'] = $minimum_duration;
			$array['billing_rates'][0]['connection_fee'] = $connection_fee;
			$array['billing_rates'][0]['currency'] = $currency;
			$array['billing_rates'][0]['enabled'] = $enabled;
			if ($action == "add") {
				$array['billing_rates'][0]['insert_date'] = 'now()';
				$array['billing_rates'][0]['insert_user'] = $_SESSION['user_uuid'];
			}

			//save to the database
			$database = new database;
			$database->app_name = 'billing';
			$database->app_uuid = 'b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f';
			if ($action == "add" && permission_exists('billing_rate_add')) {
				$database->save($array);
				message::add($text['message-add']);
			}
			if ($action == "update" && permission_exists('billing_rate_edit')) {
				$array['billing_rates'][0]['billing_rate_uuid'] = $billing_rate_uuid;
				$database->save($array);
				message::add($text['message-update']);
			}
			unset($array);

			//redirect the user
			header('Location: billing_rates.php');
			exit;
		}
	}

//(pre)load the data
	if (is_array($_POST) && @sizeof($_POST) != 0 && $_POST["persistformvar"] == "true") {
		$rate_name = $_POST["rate_name"];
		$rate_description = $_POST["rate_description"];
		$destination_prefix = $_POST["destination_prefix"];
		$rate_per_minute = $_POST["rate_per_minute"];
		$billing_increment = $_POST["billing_increment"];
		$minimum_duration = $_POST["minimum_duration"];
		$connection_fee = $_POST["connection_fee"];
		$currency = $_POST["currency"];
		$enabled = $_POST["enabled"];
	}
	else {
		if (is_uuid($billing_rate_uuid)) {
			$sql = "SELECT * FROM v_billing_rates ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND billing_rate_uuid = :billing_rate_uuid ";
			$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
			$parameters['billing_rate_uuid'] = $billing_rate_uuid;
			$database = new database;
			$row = $database->select($sql, $parameters, 'row');
			if (is_array($row) && @sizeof($row) != 0) {
				$rate_name = $row["rate_name"];
				$rate_description = $row["rate_description"];
				$destination_prefix = $row["destination_prefix"];
				$rate_per_minute = $row["rate_per_minute"];
				$billing_increment = $row["billing_increment"];
				$minimum_duration = $row["minimum_duration"];
				$connection_fee = $row["connection_fee"];
				$currency = $row["currency"];
				$enabled = $row["enabled"];
			}
			unset($sql, $parameters, $row);
		}
	}

//set default values
	if (empty($billing_increment)) { $billing_increment = 60; }
	if (empty($minimum_duration)) { $minimum_duration = 0; }
	if (empty($connection_fee)) { $connection_fee = 0; }
	if (empty($currency)) { $currency = 'USD'; }
	if (empty($enabled)) { $enabled = 'true'; }

//create token
	$object = new token;
	$token = $object->create($_SERVER['PHP_SELF']);

//include the header
	if ($action == "update") {
		$document['title'] = $text['title-billing_rate-edit'];
	}
	elseif ($action == "add") {
		$document['title'] = $text['title-billing_rate-add'];
	}
	require_once "resources/header.php";

//show the content
	echo "<form name='frm' id='frm' method='post'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap='nowrap' valign='top'><b>" . $text['title-billing_rate-edit'] . "</b><br><br></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='" . $text['button-back'] . "' onclick=\"window.location='billing_rates.php'\" value='" . $text['button-back'] . "'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='" . $text['button-save'] . "'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-rate_name'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='rate_name' maxlength='255' value=\"" . escape($rate_name) . "\">\n";
	echo "<br />\n";
	echo $text['description-rate_name'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-rate_description'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea class='formfld' name='rate_description' rows='4'>" . escape($rate_description) . "</textarea>\n";
	echo "<br />\n";
	echo $text['description-rate_description'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-destination_prefix'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_prefix' maxlength='255' value=\"" . escape($destination_prefix) . "\">\n";
	echo "<br />\n";
	echo $text['description-destination_prefix'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-rate_per_minute'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' step='0.0001' name='rate_per_minute' value=\"" . escape($rate_per_minute) . "\">\n";
	echo "<br />\n";
	echo $text['description-rate_per_minute'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-billing_increment'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' name='billing_increment' value=\"" . escape($billing_increment) . "\">\n";
	echo "<br />\n";
	echo $text['description-billing_increment'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-minimum_duration'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' name='minimum_duration' value=\"" . escape($minimum_duration) . "\">\n";
	echo "<br />\n";
	echo $text['description-minimum_duration'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-connection_fee'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' step='0.0001' name='connection_fee' value=\"" . escape($connection_fee) . "\">\n";
	echo "<br />\n";
	echo $text['description-connection_fee'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-currency'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='currency' maxlength='10' value=\"" . escape($currency) . "\">\n";
	echo "<br />\n";
	echo $text['description-currency'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-enabled'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='enabled'>\n";
	echo "		<option value='true' " . ($enabled == 'true' ? "selected='selected'" : null) . ">" . $text['option-true'] . "</option>\n";
	echo "		<option value='false' " . ($enabled == 'false' ? "selected='selected'" : null) . ">" . $text['option-false'] . "</option>\n";
	echo "	</select>\n";
	echo "<br />\n";
	echo $text['description-enabled'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "<br><br>";

	echo "<input type='hidden' name='id' value='" . escape($billing_rate_uuid) . "'>\n";
	echo "<input type='hidden' name='" . $token['name'] . "' value='" . $token['hash'] . "'>\n";

	echo "</form>";

//include the footer
	require_once "resources/footer.php";

?>
