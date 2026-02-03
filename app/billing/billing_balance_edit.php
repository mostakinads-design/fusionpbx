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
	if (permission_exists('billing_balance_add') || permission_exists('billing_balance_edit')) {
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
		$billing_balance_uuid = $_REQUEST["id"];
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (is_array($_POST) && @sizeof($_POST) != 0) {
		$extension_uuid = $_POST["extension_uuid"];
		$balance = $_POST["balance"];
		$currency = $_POST["currency"];
		$low_balance_alert = $_POST["low_balance_alert"];
		$low_balance_threshold = $_POST["low_balance_threshold"];
		$rate_plans = $_POST["rate_plans"] ?? [];
	}

//process the http post
	if (is_array($_POST) && @sizeof($_POST) != 0 && $_POST["persistformvar"] != "true") {

		//validate the token
		$token = new token;
		if (!$token->validate($_SERVER['PHP_SELF'])) {
			message::add($text['message-invalid_token'],'negative');
			header('Location: billing_balances.php');
			exit;
		}

		//check for required data
		$msg = '';
		if (strlen($extension_uuid) == 0) { $msg .= $text['message-required'] . " " . $text['label-extension'] . "<br>\n"; }
		if (strlen($balance) == 0) { $msg .= $text['message-required'] . " " . $text['label-balance'] . "<br>\n"; }
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
			$array['billing_balances'][0]['domain_uuid'] = $_SESSION['domain_uuid'];
			if ($action == "add" && permission_exists('billing_balance_add')) {
				$billing_balance_uuid = uuid();
				$array['billing_balances'][0]['billing_balance_uuid'] = $billing_balance_uuid;
			}
			$array['billing_balances'][0]['extension_uuid'] = $extension_uuid;
			$array['billing_balances'][0]['balance'] = $balance;
			$array['billing_balances'][0]['currency'] = $currency;
			$array['billing_balances'][0]['low_balance_alert'] = $low_balance_alert == 'true' ? 'true' : 'false';
			$array['billing_balances'][0]['low_balance_threshold'] = $low_balance_threshold;
			$array['billing_balances'][0]['last_updated'] = 'now()';

			//save to the database
			$database = new database;
			$database->app_name = 'billing';
			$database->app_uuid = 'b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f';
			if ($action == "add" && permission_exists('billing_balance_add')) {
				$database->save($array);
				message::add($text['message-add']);
			}
			if ($action == "update" && permission_exists('billing_balance_edit')) {
				$array['billing_balances'][0]['billing_balance_uuid'] = $billing_balance_uuid;
				$database->save($array);
				message::add($text['message-update']);
			}
			unset($array);

			//delete existing user rates for this extension
			$sql = "DELETE FROM v_billing_user_rates ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND extension_uuid = :extension_uuid ";
			$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
			$parameters['extension_uuid'] = $extension_uuid;
			$database->execute($sql, $parameters);
			unset($sql, $parameters);

			//insert new user rates
			if (is_array($rate_plans) && count($rate_plans) > 0) {
				$array = [];
				$i = 0;
				foreach ($rate_plans as $rate_uuid) {
					if (is_uuid($rate_uuid)) {
						$array['billing_user_rates'][$i]['billing_user_rate_uuid'] = uuid();
						$array['billing_user_rates'][$i]['domain_uuid'] = $_SESSION['domain_uuid'];
						$array['billing_user_rates'][$i]['extension_uuid'] = $extension_uuid;
						$array['billing_user_rates'][$i]['billing_rate_uuid'] = $rate_uuid;
						$array['billing_user_rates'][$i]['enabled'] = 'true';
						$array['billing_user_rates'][$i]['insert_date'] = 'now()';
						$i++;
					}
				}
				if (count($array) > 0) {
					$database->app_name = 'billing';
					$database->app_uuid = 'b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f';
					$database->save($array);
				}
				unset($array);
			}

			//redirect the user
			header('Location: billing_balances.php');
			exit;
		}
	}

//(pre)load the data
	if (is_array($_POST) && @sizeof($_POST) != 0 && $_POST["persistformvar"] == "true") {
		$extension_uuid = $_POST["extension_uuid"];
		$balance = $_POST["balance"];
		$currency = $_POST["currency"];
		$low_balance_alert = $_POST["low_balance_alert"];
		$low_balance_threshold = $_POST["low_balance_threshold"];
		$rate_plans = $_POST["rate_plans"] ?? [];
	}
	else {
		if (is_uuid($billing_balance_uuid)) {
			$sql = "SELECT * FROM v_billing_balances ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND billing_balance_uuid = :billing_balance_uuid ";
			$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
			$parameters['billing_balance_uuid'] = $billing_balance_uuid;
			$database = new database;
			$row = $database->select($sql, $parameters, 'row');
			if (is_array($row) && @sizeof($row) != 0) {
				$extension_uuid = $row["extension_uuid"];
				$balance = $row["balance"];
				$currency = $row["currency"];
				$low_balance_alert = $row["low_balance_alert"];
				$low_balance_threshold = $row["low_balance_threshold"];
			}
			unset($sql, $parameters, $row);

			//get assigned rate plans
			$sql = "SELECT billing_rate_uuid FROM v_billing_user_rates ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND extension_uuid = :extension_uuid ";
			$sql .= "AND enabled = true ";
			$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
			$parameters['extension_uuid'] = $extension_uuid;
			$result = $database->select($sql, $parameters, 'all');
			$rate_plans = [];
			if (is_array($result) && count($result) > 0) {
				foreach ($result as $row) {
					$rate_plans[] = $row['billing_rate_uuid'];
				}
			}
			unset($sql, $parameters, $result);
		}
	}

//set default values
	if (empty($balance)) { $balance = 0; }
	if (empty($currency)) { $currency = 'USD'; }
	if (empty($low_balance_alert)) { $low_balance_alert = 'false'; }
	if (empty($low_balance_threshold)) { $low_balance_threshold = 5; }
	if (!is_array($rate_plans)) { $rate_plans = []; }

//get extensions list
	$sql = "SELECT extension_uuid, extension, effective_caller_id_name ";
	$sql .= "FROM v_extensions ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	$sql .= "ORDER BY extension ASC ";
	$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
	$database = new database;
	$extensions = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);

//get available rate plans
	$sql = "SELECT billing_rate_uuid, rate_name, destination_prefix, rate_per_minute, currency ";
	$sql .= "FROM v_billing_rates ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	$sql .= "AND enabled = true ";
	$sql .= "ORDER BY rate_name ASC ";
	$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
	$available_rates = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);

//create token
	$object = new token;
	$token = $object->create($_SERVER['PHP_SELF']);

//include the header
	if ($action == "update") {
		$document['title'] = $text['title-billing_balance-edit'] ?? "Billing Balance - Edit";
	}
	elseif ($action == "add") {
		$document['title'] = $text['title-billing_balance-add'] ?? "Billing Balance - Add";
	}
	require_once "resources/header.php";

//show the content
	echo "<form name='frm' id='frm' method='post'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap='nowrap' valign='top'><b>" . $document['title'] . "</b><br><br></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='" . $text['button-back'] . "' onclick=\"window.location='billing_balances.php'\" value='" . $text['button-back'] . "'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='" . $text['button-save'] . "'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-extension'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	if ($action == "add") {
		echo "	<select class='formfld' name='extension_uuid' required>\n";
		echo "		<option value=''>" . ($text['label-select'] ?? 'Select Extension') . "</option>\n";
		if (is_array($extensions) && count($extensions) > 0) {
			foreach ($extensions as $ext) {
				$selected = ($extension_uuid == $ext['extension_uuid']) ? "selected='selected'" : "";
				$display_name = $ext['extension'];
				if (!empty($ext['effective_caller_id_name'])) {
					$display_name .= " - " . $ext['effective_caller_id_name'];
				}
				echo "		<option value='" . $ext['extension_uuid'] . "' " . $selected . ">" . escape($display_name) . "</option>\n";
			}
		}
		echo "	</select>\n";
	}
	else {
		//find the extension number for display
		$ext_display = "";
		if (is_array($extensions) && count($extensions) > 0) {
			foreach ($extensions as $ext) {
				if ($ext['extension_uuid'] == $extension_uuid) {
					$ext_display = $ext['extension'];
					if (!empty($ext['effective_caller_id_name'])) {
						$ext_display .= " - " . $ext['effective_caller_id_name'];
					}
					break;
				}
			}
		}
		echo "	<strong>" . escape($ext_display) . "</strong>\n";
		echo "	<input type='hidden' name='extension_uuid' value='" . escape($extension_uuid) . "'>\n";
	}
	echo "<br />\n";
	echo ($text['description-extension_select'] ?? 'Select the extension/user for this billing balance.') . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-balance'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' step='0.0001' name='balance' value=\"" . escape($balance) . "\" required>\n";
	echo "<br />\n";
	echo ($text['description-balance'] ?? 'Current prepaid balance amount.') . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-currency'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='currency' maxlength='10' value=\"" . escape($currency) . "\">\n";
	echo "<br />\n";
	echo ($text['description-currency'] ?? 'Currency code (USD, EUR, GBP, etc.).') . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-low_balance_alert'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='low_balance_alert'>\n";
	echo "		<option value='true' " . ($low_balance_alert == 'true' ? "selected='selected'" : null) . ">" . ($text['option-enabled'] ?? 'Enabled') . "</option>\n";
	echo "		<option value='false' " . ($low_balance_alert == 'false' ? "selected='selected'" : null) . ">" . ($text['option-disabled'] ?? 'Disabled') . "</option>\n";
	echo "	</select>\n";
	echo "<br />\n";
	echo ($text['description-low_balance_alert'] ?? 'Enable or disable low balance notifications.') . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . $text['label-low_balance_threshold'] . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' step='0.0001' name='low_balance_threshold' value=\"" . escape($low_balance_threshold) . "\">\n";
	echo "<br />\n";
	echo ($text['description-low_balance_threshold'] ?? 'Alert threshold when balance falls below this amount.') . "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	" . ($text['label-assigned_rate_plans'] ?? 'Assigned Rate Plans') . "\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	if (is_array($available_rates) && count($available_rates) > 0) {
		echo "	<select class='formfld' name='rate_plans[]' multiple size='10' style='min-width: 400px;'>\n";
		foreach ($available_rates as $rate) {
			$selected = in_array($rate['billing_rate_uuid'], $rate_plans) ? "selected='selected'" : "";
			$rate_display = escape($rate['rate_name']) . " (" . ($text['label-prefix'] ?? 'Prefix') . ": " . escape($rate['destination_prefix']) . ", " . ($text['label-rate'] ?? 'Rate') . ": " . number_format($rate['rate_per_minute'], 4) . " " . escape($rate['currency']) . "/min)";
			echo "		<option value='" . $rate['billing_rate_uuid'] . "' " . $selected . ">" . $rate_display . "</option>\n";
		}
		echo "	</select>\n";
		echo "<br />\n";
		echo ($text['description-assigned_rate_plans'] ?? 'Select one or more rate plans to assign to this extension. Hold Ctrl (Windows) or Cmd (Mac) to select multiple.') . "\n";
	}
	else {
		echo "	<em>" . ($text['message-no_rate_plans'] ?? 'No rate plans available. Please create rate plans first.') . "</em>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "<br><br>";

	echo "<input type='hidden' name='id' value='" . escape($billing_balance_uuid) . "'>\n";
	echo "<input type='hidden' name='" . $token['name'] . "' value='" . $token['hash'] . "'>\n";

	echo "</form>";

//include the footer
	require_once "resources/footer.php";

?>
