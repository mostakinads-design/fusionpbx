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
	if (permission_exists('billing_usage_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//handle search and filters
	$search = $_GET['search'] ?? '';
	$extension_filter = $_GET['extension_uuid'] ?? '';
	$date_from = $_GET['date_from'] ?? '';
	$date_to = $_GET['date_to'] ?? '';

//get order and order_by
	$order_by = $_GET["order_by"] ?? 'call_date';
	$order = $_GET["order"] ?? 'DESC';

//prepare to page the results
	$sql = "SELECT count(*) FROM v_billing_usage u ";
	$sql .= "LEFT JOIN v_extensions e ON u.extension_uuid = e.extension_uuid ";
	$sql .= "WHERE u.domain_uuid = :domain_uuid ";
	$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
	
	//filter by extension if provided
	if (!empty($extension_filter)) {
		$sql .= "AND u.extension_uuid = :extension_uuid ";
		$parameters['extension_uuid'] = $extension_filter;
	}
	
	//filter by date range
	if (!empty($date_from)) {
		$sql .= "AND u.call_date >= :date_from ";
		$parameters['date_from'] = $date_from . ' 00:00:00';
	}
	if (!empty($date_to)) {
		$sql .= "AND u.call_date <= :date_to ";
		$parameters['date_to'] = $date_to . ' 23:59:59';
	}
	
	//search
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "u.destination_number LIKE :search ";
		$sql .= "OR u.matched_prefix LIKE :search ";
		$sql .= "OR e.extension LIKE :search ";
		$sql .= ") ";
		$parameters['search'] = '%'.$search.'%';
	}
	
	$database = new database;
	$num_rows = $database->select($sql, $parameters, 'column');

//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "&search=" . urlencode($search);
	$param .= "&extension_uuid=" . urlencode($extension_filter);
	$param .= "&date_from=" . urlencode($date_from);
	$param .= "&date_to=" . urlencode($date_to);
	$page = is_numeric($_GET['page']) ? $_GET['page'] : 0;
	list($paging_controls, $rows_per_page) = paging($num_rows, $param, $rows_per_page);
	list($paging_controls_mini, $rows_per_page) = paging($num_rows, $param, $rows_per_page, true);
	$offset = $rows_per_page * $page;

//get the list
	$sql = "SELECT u.billing_usage_uuid, u.extension_uuid, e.extension, ";
	$sql .= "u.destination_number, u.matched_prefix, u.duration, ";
	$sql .= "u.billable_duration, u.rate_applied, u.connection_fee, ";
	$sql .= "u.cost, u.currency, u.call_date ";
	$sql .= "FROM v_billing_usage u ";
	$sql .= "LEFT JOIN v_extensions e ON u.extension_uuid = e.extension_uuid ";
	$sql .= "WHERE u.domain_uuid = :domain_uuid ";
	
	//apply same filters
	if (!empty($extension_filter)) {
		$sql .= "AND u.extension_uuid = :extension_uuid ";
	}
	if (!empty($date_from)) {
		$sql .= "AND u.call_date >= :date_from ";
	}
	if (!empty($date_to)) {
		$sql .= "AND u.call_date <= :date_to ";
	}
	if (!empty($search)) {
		$sql .= "AND (";
		$sql .= "u.destination_number LIKE :search ";
		$sql .= "OR u.matched_prefix LIKE :search ";
		$sql .= "OR e.extension LIKE :search ";
		$sql .= ") ";
	}
	
	$sql .= order_by($order_by, $order, 'u.call_date', 'DESC');
	$sql .= limit_offset($rows_per_page, $offset);
	$result = $database->select($sql, $parameters, 'all');
	unset($sql);

//calculate totals
	$sql_totals = "SELECT SUM(u.cost) as total_cost, SUM(u.duration) as total_duration ";
	$sql_totals .= "FROM v_billing_usage u ";
	$sql_totals .= "WHERE u.domain_uuid = :domain_uuid ";
	if (!empty($extension_filter)) {
		$sql_totals .= "AND u.extension_uuid = :extension_uuid ";
	}
	if (!empty($date_from)) {
		$sql_totals .= "AND u.call_date >= :date_from ";
	}
	if (!empty($date_to)) {
		$sql_totals .= "AND u.call_date <= :date_to ";
	}
	if (!empty($search)) {
		$sql_totals .= "AND (";
		$sql_totals .= "u.destination_number LIKE :search ";
		$sql_totals .= "OR u.matched_prefix LIKE :search ";
		$sql_totals .= ") ";
	}
	$totals = $database->select($sql_totals, $parameters, 'row');
	unset($sql_totals, $parameters);

//get extensions for filter dropdown
	$sql_ext = "SELECT extension_uuid, extension, effective_caller_id_name FROM v_extensions ";
	$sql_ext .= "WHERE domain_uuid = :domain_uuid ";
	$sql_ext .= "ORDER BY extension ASC";
	$params_ext['domain_uuid'] = $_SESSION['domain_uuid'];
	$extensions = $database->select($sql_ext, $params_ext, 'all');
	unset($sql_ext, $params_ext);

//include the header
	$document['title'] = $text['title-billing_usage'];
	require_once "resources/header.php";

//show the content
	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>" . $text['title-billing_usage'] . "</b></div>\n";
	echo "	<div class='actions'>\n";
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo "<form id='form_filter' class='inline' method='get'>\n";
	echo "<table cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr>\n";
	
	//extension filter
	echo "<td style='padding-right: 10px;'>\n";
	echo "	<select name='extension_uuid' class='formfld' style='width: 150px;'>\n";
	echo "		<option value=''>All Extensions</option>\n";
	if (is_array($extensions)) {
		foreach ($extensions as $ext) {
			$selected = ($extension_filter == $ext['extension_uuid']) ? "selected='selected'" : "";
			echo "		<option value='" . $ext['extension_uuid'] . "' " . $selected . ">" . escape($ext['extension']) . " - " . escape($ext['effective_caller_id_name']) . "</option>\n";
		}
	}
	echo "	</select>\n";
	echo "</td>\n";
	
	//date from
	echo "<td style='padding-right: 10px;'>\n";
	echo "	<input type='date' name='date_from' class='formfld' style='width: 120px;' value='" . escape($date_from) . "' placeholder='From Date'>\n";
	echo "</td>\n";
	
	//date to
	echo "<td style='padding-right: 10px;'>\n";
	echo "	<input type='date' name='date_to' class='formfld' style='width: 120px;' value='" . escape($date_to) . "' placeholder='To Date'>\n";
	echo "</td>\n";
	
	//search
	echo "<td style='padding-right: 10px;'>\n";
	echo "	<input type='text' class='formfld' style='width: 150px;' name='search' id='search' value='" . escape($search) . "' placeholder='Search'>\n";
	echo "</td>\n";
	
	//filter button
	echo "<td>\n";
	echo "	<input type='submit' class='btn' name='submit' value='Filter'>\n";
	echo "</td>\n";
	
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "<br />\n";

	//show totals
	if ($totals) {
		echo "<div style='padding: 10px; background-color: #f0f0f0; margin-bottom: 10px;'>\n";
		echo "	<b>Totals:</b> ";
		echo "	Total Calls: " . number_format($num_rows) . " | ";
		echo "	Total Duration: " . gmdate("H:i:s", $totals['total_duration']) . " | ";
		echo "	Total Cost: " . number_format($totals['total_cost'], 4) . "\n";
		echo "</div>\n";
	}

	echo $paging_controls_mini;

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('extension', $text['label-extension'], $order_by, $order);
	echo th_order_by('destination_number', $text['label-destination_number'], $order_by, $order);
	echo th_order_by('matched_prefix', $text['label-matched_prefix'], $order_by, $order);
	echo th_order_by('duration', $text['label-duration'], $order_by, $order);
	echo th_order_by('billable_duration', 'Billable', $order_by, $order);
	echo th_order_by('rate_applied', 'Rate', $order_by, $order);
	echo th_order_by('connection_fee', 'Conn. Fee', $order_by, $order);
	echo th_order_by('cost', $text['label-cost'], $order_by, $order);
	echo th_order_by('call_date', $text['label-call_date'], $order_by, $order);
	echo "</tr>\n";

	if (is_array($result) && @sizeof($result) != 0) {
		foreach($result as $row) {
			echo "<tr>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['extension']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['destination_number']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['matched_prefix']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . gmdate("H:i:s", $row['duration']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . gmdate("H:i:s", $row['billable_duration']) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . number_format($row['rate_applied'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . number_format($row['connection_fee'], 4) . "</td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'><b>" . number_format($row['cost'], 4) . " " . escape($row['currency']) . "</b></td>\n";
			echo "	<td valign='top' class='" . $row_style[$c] . "'>" . escape($row['call_date']) . "</td>\n";
			echo "</tr>\n";
			$c = $c == 0 ? 1 : 0;
		}
	}
	else {
		echo "<tr>\n";
		echo "	<td colspan='9' align='center'>No usage records found</td>\n";
		echo "</tr>\n";
	}
	unset($result);

	echo "</table>\n";
	echo "<br />\n";
	echo $paging_controls;
	echo "<br /><br />\n";

//include the footer
	require_once "resources/footer.php";

?>
