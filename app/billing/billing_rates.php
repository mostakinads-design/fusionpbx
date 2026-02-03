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
	if (permission_exists('billing_rate_view')) {
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
	$order_by = $_GET["order_by"] ?? 'rate_name';
	$order = $_GET["order"] ?? 'ASC';

//prepare to page the results
	$sql = "SELECT count(*) FROM v_billing_rates ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "rate_name LIKE :search ";
		$sql .= "OR rate_description LIKE :search ";
		$sql .= "OR destination_prefix LIKE :search ";
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
	$sql = "SELECT billing_rate_uuid, rate_name, rate_description, destination_prefix, ";
	$sql .= "rate_per_minute, billing_increment, minimum_duration, connection_fee, ";
	$sql .= "currency, enabled, insert_date ";
	$sql .= "FROM v_billing_rates ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "rate_name LIKE :search ";
		$sql .= "OR rate_description LIKE :search ";
		$sql .= "OR destination_prefix LIKE :search ";
		$sql .= ") ";
	}
	$sql .= order_by($order_by, $order, 'rate_name', 'ASC');
	$sql .= limit_offset($rows_per_page, $offset);
	$result = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);

//create token
	$object = new token;
	$token = $object->create($_SERVER['PHP_SELF']);

//include the header
	$document['title'] = $text['title-billing_rates'];
	require_once "resources/header.php";

//show the content
	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>" . $text['title-billing_rates'] . "</b></div>\n";
	echo "	<div class='actions'>\n";
	if (permission_exists('billing_rate_add')) {
		echo button::create(['type'=>'button','label'=>$text['button-add'],'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','link'=>'billing_rate_edit.php']);
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
	echo th_order_by('rate_name', $text['label-rate_name'], $order_by, $order);
	echo th_order_by('destination_prefix', $text['label-destination_prefix'], $order_by, $order);
	echo th_order_by('rate_per_minute', $text['label-rate_per_minute'], $order_by, $order);
	echo th_order_by('billing_increment', $text['label-billing_increment'], $order_by, $order);
	echo th_order_by('connection_fee', $text['label-connection_fee'], $order_by, $order);
	echo th_order_by('currency', $text['label-currency'], $order_by, $order);
	echo th_order_by('enabled', $text['label-enabled'], $order_by, $order);
	echo "<td class='list_control_icon'>";
	if (permission_exists('billing_rate_edit') && $result) {
		echo "	<a href='billing_rate_edit.php' title='" . $text['button-add'] . "'>" . $v_link_label_add . "</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if (is_array($result) && @sizeof($result) != 0) {
		foreach($result as $row) {
			$tr_link = (permission_exists('billing_rate_edit')) ? "href='billing_rate_edit.php?id=" . urlencode($row['billing_rate_uuid']) . "'" : null;
			echo "<tr " . $tr_link . ">\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['rate_name']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['destination_prefix']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . number_format($row['rate_per_minute'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . $row['billing_increment'] . "s</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . number_format($row['connection_fee'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['currency']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . ($row['enabled'] == 't' || $row['enabled'] == '1' ? $text['label-true'] : $text['label-false']) . "</td>\n";
			echo "	<td class='list_control_icon'>";
			if (permission_exists('billing_rate_edit')) {
				echo "		<a href='billing_rate_edit.php?id=" . urlencode($row['billing_rate_uuid']) . "' title='" . $text['button-edit'] . "'>" . $v_link_label_edit . "</a>";
			}
			if (permission_exists('billing_rate_delete')) {
				echo "		<a href='billing_rate_delete.php?id=" . urlencode($row['billing_rate_uuid']) . "&" . $token . "' title='" . $text['button-delete'] . "' onclick=\"return confirm('" . $text['confirm-delete'] . "')\">" . $v_link_label_delete . "</a>";
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
