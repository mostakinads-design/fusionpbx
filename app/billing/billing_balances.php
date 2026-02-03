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
	if (permission_exists('billing_balance_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//handle search
	$search = $_GET['search'] ?? '';

//get order and order_by
	$order_by = $_GET["order_by"] ?? 'extension';
	$order = $_GET["order"] ?? 'ASC';

//prepare to page the results
	$sql = "SELECT count(*) FROM v_billing_balances b ";
	$sql .= "LEFT JOIN v_extensions e ON b.extension_uuid = e.extension_uuid ";
	$sql .= "WHERE b.domain_uuid = :domain_uuid ";
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "e.extension LIKE :search ";
		$sql .= "OR e.effective_caller_id_name LIKE :search ";
		$sql .= "OR CAST(b.balance AS TEXT) LIKE :search ";
		$sql .= ") ";
		$parameters['search'] = '%'.$search.'%';
	}
	$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
	$database = new database;
	$num_rows = $database->select($sql, $parameters, 'column');

//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "&search=" . urlencode($search);
	$page = is_numeric($_GET['page']) ? $_GET['page'] : 0;
	list($paging_controls, $rows_per_page) = paging($num_rows, $param, $rows_per_page);
	list($paging_controls_mini, $rows_per_page) = paging($num_rows, $param, $rows_per_page, true);
	$offset = $rows_per_page * $page;

//get the list
	$sql = "SELECT b.billing_balance_uuid, b.extension_uuid, e.extension, ";
	$sql .= "e.effective_caller_id_name, b.balance, b.currency, ";
	$sql .= "b.low_balance_alert, b.low_balance_threshold, b.last_updated ";
	$sql .= "FROM v_billing_balances b ";
	$sql .= "LEFT JOIN v_extensions e ON b.extension_uuid = e.extension_uuid ";
	$sql .= "WHERE b.domain_uuid = :domain_uuid ";
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "e.extension LIKE :search ";
		$sql .= "OR e.effective_caller_id_name LIKE :search ";
		$sql .= "OR CAST(b.balance AS TEXT) LIKE :search ";
		$sql .= ") ";
	}
	$sql .= order_by($order_by, $order, 'e.extension', 'ASC');
	$sql .= limit_offset($rows_per_page, $offset);
	$result = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);

//create token
	$object = new token;
	$token = $object->create($_SERVER['PHP_SELF']);

//include the header
	$document['title'] = $text['title-billing_balances'];
	require_once "resources/header.php";

//show the content
	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>" . $text['title-billing_balances'] . "</b></div>\n";
	echo "	<div class='actions'>\n";
	if (permission_exists('billing_balance_add')) {
		echo button::create(['type'=>'button','label'=>$text['button-add'],'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','link'=>'billing_balance_edit.php']);
	}
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo "<form id='form_search' class='inline' method='get'>\n";
	echo "<input type='text' class='txt' style='width: 150px; margin-right: 3px;' name='search' id='search' value='" . escape($search) . "'>\n";
	echo "<input type='submit' class='btn' name='submit' value='" . $text['button-search'] . "'>\n";
	echo "</form>\n";
	echo "<br /><br />\n";

	echo $paging_controls_mini;

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('extension', $text['label-extension'], $order_by, $order);
	echo th_order_by('effective_caller_id_name', 'Name', $order_by, $order);
	echo th_order_by('balance', $text['label-balance'], $order_by, $order);
	echo th_order_by('currency', $text['label-currency'], $order_by, $order);
	echo th_order_by('low_balance_threshold', $text['label-low_balance_threshold'], $order_by, $order);
	echo th_order_by('last_updated', 'Last Updated', $order_by, $order);
	echo "<td class='list_control_icon'>";
	if (permission_exists('billing_balance_edit') && $result) {
		echo "	<a href='billing_balance_edit.php' title='" . $text['button-add'] . "'>" . $v_link_label_add . "</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if (is_array($result) && @sizeof($result) != 0) {
		foreach($result as $row) {
			$tr_link = (permission_exists('billing_balance_edit')) ? "href='billing_balance_edit.php?id=" . urlencode($row['billing_balance_uuid']) . "'" : null;
			echo "<tr " . $tr_link . ">\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['extension']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['effective_caller_id_name']) . "</td>\n";
			$balance_class = ($row['balance'] < $row['low_balance_threshold']) ? "style='color: red; font-weight: bold;'" : "";
			echo "	<td valign='top' class='" . $row_style[$c] . "' " . $balance_class . ">" . number_format($row['balance'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['currency']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . number_format($row['low_balance_threshold'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['last_updated']) . "</td>\n";
			echo "	<td class='list_control_icon'>";
			if (permission_exists('billing_balance_edit')) {
				echo "		<a href='billing_balance_edit.php?id=" . urlencode($row['billing_balance_uuid']) . "' title='" . $text['button-edit'] . "'>" . $v_link_label_edit . "</a>";
			}
			if (permission_exists('billing_balance_delete')) {
				echo "		<a href='billing_balance_delete.php?id=" . urlencode($row['billing_balance_uuid']) . "&" . $token . "' title='" . $text['button-delete'] . "' onclick=\"return confirm('" . $text['confirm-delete'] . "')\">" . $v_link_label_delete . "</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			$c = $c == 0 ? 1 : 0;
		}
	}
	unset($result);

	echo "</table>\n";
	echo "<br />\n";
	echo $paging_controls;
	echo "<br /><br />\n";

//include the footer
	require_once "resources/footer.php";

?>
