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
	require_once "resources/classes/billing.php";

//check permissions
	if (permission_exists('billing_balance_edit')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the billing_balance_uuid
	if (is_uuid($_REQUEST["id"])) {
		$billing_balance_uuid = $_REQUEST["id"];
	}

//get http post variables and set them to php variables
	if (is_array($_POST) && @sizeof($_POST) != 0) {
		$transaction_type = $_POST["transaction_type"];
		$amount = $_POST["amount"];
		$description = $_POST["description"];
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
		if (strlen($amount) == 0) { $msg .= $text['message-required'] . " Amount<br>\n"; }
		if (strlen($transaction_type) == 0) { $msg .= $text['message-required'] . " Transaction Type<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persist_form_var.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

		//get the extension details
		$database = new database;
		$sql = "SELECT domain_uuid, extension_uuid FROM v_billing_balances ";
		$sql .= "WHERE billing_balance_uuid = :billing_balance_uuid ";
		$parameters['billing_balance_uuid'] = $billing_balance_uuid;
		$balance_record = $database->select($sql, $parameters, 'row');

		if ($balance_record) {
			//initialize billing class
			$billing = new billing;
			$billing->database = $database;
			$billing->domain_uuid = $balance_record['domain_uuid'];
			$billing->extension_uuid = $balance_record['extension_uuid'];

			//adjust amount based on transaction type
			$adjust_amount = floatval($amount);
			if ($transaction_type == 'debit') {
				$adjust_amount = -abs($adjust_amount);
			}
			else if ($transaction_type == 'credit') {
				$adjust_amount = abs($adjust_amount);
			}

			//update the balance
			$result = $billing->update_balance($adjust_amount, $transaction_type, $description);

			if ($result) {
				message::add('Balance adjusted successfully');
			}
			else {
				message::add('Failed to adjust balance', 'negative');
			}
		}

		//redirect the user
		header('Location: billing_balances.php');
		exit;
	}

//get the balance details
	if (is_uuid($billing_balance_uuid)) {
		$sql = "SELECT b.*, e.extension, e.effective_caller_id_name ";
		$sql .= "FROM v_billing_balances b ";
		$sql .= "LEFT JOIN v_extensions e ON b.extension_uuid = e.extension_uuid ";
		$sql .= "WHERE b.domain_uuid = :domain_uuid ";
		$sql .= "AND b.billing_balance_uuid = :billing_balance_uuid ";
		$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
		$parameters['billing_balance_uuid'] = $billing_balance_uuid;
		$database = new database;
		$balance_info = $database->select($sql, $parameters, 'row');
		unset($sql, $parameters);
	}

//create token
	$object = new token;
	$token = $object->create($_SERVER['PHP_SELF']);

//include the header
	$document['title'] = "Adjust Balance";
	require_once "resources/header.php";

//show the content
	echo "<form name='frm' id='frm' method='post'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap='nowrap' valign='top'><b>Adjust Balance</b><br><br></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='" . $text['button-back'] . "' onclick=\"window.location='billing_balances.php'\" value='" . $text['button-back'] . "'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='" . $text['button-save'] . "'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Extension\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<strong>" . escape($balance_info['extension']) . " - " . escape($balance_info['effective_caller_id_name']) . "</strong>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Current Balance\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<strong>" . number_format($balance_info['balance'], 4) . " " . escape($balance_info['currency']) . "</strong>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Transaction Type\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='transaction_type' required>\n";
	echo "		<option value=''>Select...</option>\n";
	echo "		<option value='credit'>Add Credit</option>\n";
	echo "		<option value='debit'>Deduct Credit</option>\n";
	echo "		<option value='adjustment'>Adjustment</option>\n";
	echo "	</select>\n";
	echo "<br />\n";
	echo "Select whether to add or deduct credit from the balance.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Amount\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' step='0.01' name='amount' required>\n";
	echo "<br />\n";
	echo "Enter the amount to add or deduct (always enter as a positive number).\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Description\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea class='formfld' name='description' rows='3'></textarea>\n";
	echo "<br />\n";
	echo "Optional description for this transaction.\n";
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
