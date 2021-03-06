<?php
    $title = "Quota order numbers";
	require ("includes/db.php");
	require ("includes/header.php");
?>

<!-- Start breadcrumbs //-->
<div id="wrapper" class="direction-ltr">
	<div class="gem-c-breadcrumbs govuk-breadcrumbs " data-module="track-click">
	<ol class="govuk-breadcrumbs__list">
		<li class="govuk-breadcrumbs__list-item"><a class="govuk-breadcrumbs__link" href="/">Main menu</a></li>
		<li class="govuk-breadcrumbs__list-item">Quota order numbers</li>
	</ol>
</div>
<!-- End breadcrumbs //-->

<div class="app-content__header">
	<h1 class="govuk-heading-xl nomargin">Quota order numbers</h1>
</div>

<form action="/quota_order_number_create_edit.html" method="get" class="inline_form" style="display:none">
	<input type="hidden" name="phase" value="<?=$phase?>" />
	<h3>New quota order number</h3>
	<div class="column-one-third" style="width:320px">
	<div class="govuk-form-group" style="padding:0px;margin:0px">
			<button type="submit" class="govuk-button">Create new quota order number</button>
		</div>
	</div>
	<div class="clearer"><!--&nbsp;//--></div>
</form>


<?php
	# Get all the quota order number exclusions
	$sql = "SELECT quota_order_number_origin_sid, excluded_geographical_area_sid, ga.description
	FROM quota_order_number_origin_exclusions qonoe, ml.ml_geographical_areas ga
	WHERE qonoe.excluded_geographical_area_sid = ga.geographical_area_sid";
	$result = pg_query($conn, $sql);
	$quota_order_number_origin_exclusions = array();
	if ($result) {
		while ($row = pg_fetch_array($result)) {
			$quota_order_number_origin_sid      = $row['quota_order_number_origin_sid'];
			$excluded_geographical_area_sid     = $row['excluded_geographical_area_sid'];
			$description                        = $row['description'];
			$qonoe = new quota_order_number_origin_exclusion;
			$qonoe->set_properties($quota_order_number_origin_sid, $excluded_geographical_area_sid, $description);
			array_push($quota_order_number_origin_exclusions, $qonoe);
		}
	}

	# Get the complete list of quota order number origins
	$sql = "SELECT qono.quota_order_number_origin_sid, qono.quota_order_number_sid, qono.geographical_area_id, ga.description, qon.quota_order_number_id
	FROM quota_order_number_origins qono, ml.ml_geographical_areas ga, quota_order_numbers qon
	WHERE ga.geographical_area_id = qono.geographical_area_id
	AND qon.quota_order_number_sid = qono.quota_order_number_sid
	AND (qono.validity_end_date IS NULL OR qono.validity_end_date > CURRENT_DATE)
	ORDER BY qono.quota_order_number_sid, ga.description";

	$result = pg_query($conn, $sql);
	$quota_order_number_origins = array();
	if ($result) {
		while ($row = pg_fetch_array($result)) {
			$geographical_area_id           = $row['geographical_area_id'];
			$quota_order_number_origin_sid  = $row['quota_order_number_origin_sid'];
			$quota_order_number_sid         = $row['quota_order_number_sid'];
			$quota_order_number_id          = $row['quota_order_number_id'];
			$description                    = $row['description'];
			$qono = new quota_order_number_origin;
			$qono->set_properties($quota_order_number_origin_sid, $geographical_area_id, $quota_order_number_id, $quota_order_number_sid, $description);
			$qonoe_count = count($quota_order_number_origin_exclusions);
			for($i = 0; $i < $qonoe_count; $i++) {
				$t = $quota_order_number_origin_exclusions[$i];
				if ($t->quota_order_number_origin_sid == $quota_order_number_origin_sid) {
					array_push($qono->exclusions, $t);
					#p ("Adding exclusion");
				}
			}
			array_push($quota_order_number_origins, $qono);
		}
	}

	# Get the complete list of quota definitions
	$sql = "SELECT validity_start_date, validity_end_date, quota_order_number_id FROM quota_definitions
	WHERE validity_start_date >= '2018-01-01' ORDER BY 3, 1 DESC";

	$result = pg_query($conn, $sql);
	$quota_definitions = array();
	if ($result) {
		while ($row = pg_fetch_array($result)) {
			$quota_order_number_id	= $row['quota_order_number_id'];
			$validity_start_date	= $row['validity_start_date'];
			$validity_end_date		= $row['validity_end_date'];
			$qd = new quota_definition;
			$qd->quota_order_number_id	= $quota_order_number_id;
			$qd->validity_start_date	= $validity_start_date;
			$qd->validity_end_date		= $validity_end_date;
			array_push($quota_definitions, $qd);
		}
	}

	# Get the complete list of quota order numbers
	$sql = "SELECT quota_order_number_sid, quota_order_number_id, validity_start_date, validity_end_date
	FROM quota_order_numbers /* WHERE (validity_end_date IS NULL OR validity_end_date > CURRENT_DATE) */
	ORDER BY quota_order_number_id";
	$result = pg_query($conn, $sql);
	$quota_order_numbers = array();
	if ($result) {
		while ($row = pg_fetch_array($result)) {
			$quota_order_number_sid = $row['quota_order_number_sid'];
			$quota_order_number_id  = $row['quota_order_number_id'];
			$validity_start_date    = $row['validity_start_date'];
			$validity_end_date      = $row['validity_end_date'];
			$rowclass               = rowclass($validity_start_date, $validity_end_date);

			$qon = new quota_order_number;
			$qon->set_properties($quota_order_number_id, $validity_start_date, $validity_end_date);
			$qon->measure_types = array();
			$qono_count = count($quota_order_number_origins);
			for($i = 0; $i < $qono_count; $i++) {
				$t = $quota_order_number_origins[$i];
				if ($t->quota_order_number_sid == $quota_order_number_sid) {
					array_push($qon->origins, $t);
				}
			}

			# Add the quota to the main list
			array_push($quota_order_numbers, $qon);
		}

		$sql = "select distinct measure_type_id, ordernumber
		from measures where ordernumber in (
		SELECT quota_order_number_id
		FROM quota_order_numbers WHERE (validity_end_date IS NULL OR validity_end_date > CURRENT_DATE)
		)
		order by 1, 2";
		$result = pg_query($conn, $sql);

		if ($result) {
			while ($row = pg_fetch_array($result)) {
				foreach ($quota_order_numbers as $quota_order_number) {
					if ($row['ordernumber'] == $quota_order_number->quota_order_number_id) {
						array_push($quota_order_number->measure_types, $row['measure_type_id']);
						break;
					}
				}
			}
		}


?>
<h2 id="fcfs">FCFS quotas</h2>
<p>The table below is a list of all of the First-Come-First-Served (FCFS) quotas in place. Licensed quotas
	are not managed via this quota order mechanism.
</p>
<table class="govuk-table" cellspacing="0">
<thead class="govuk-table__head">
	<tr class="govuk-table__row">
		<th class="govuk-table__header" scope="col" style="width:10%">Order number</th>
		<th class="govuk-table__header c" scope="col" style="width:10%">EU Link</th>
		<th class="govuk-table__header" scope="col" style="width:16%">Origins</th>
		<th class="govuk-table__header" scope="col" style="width:15%">Measure type(s)</th>
		<th class="govuk-table__header" scope="col" style="width:17%">Start date</th>
		<th class="govuk-table__header" scope="col" style="width:17%">End date</th>
<!--
		<th class="govuk-table__header" scope="col" style="width:26%">Definition periods</th>
//-->		
		<th class="govuk-table__header r" scope="col" style="width:16%">Actions</th>
	</tr>
	</thead>
<?php
		$qon_count = count($quota_order_numbers);
		for($i = 0; $i < $qon_count; $i++) {
			$t                      = $quota_order_numbers[$i];
			$quota_order_number_id  = $t->quota_order_number_id;
			$validity_start_date    = short_date($t->validity_start_date);
			$validity_end_date      = short_date($t->validity_end_date);
			$rowclass               = rowclass($validity_start_date, $validity_end_date);
			$measure_types			= $t->get_measure_types();
			$origins    = "";
			$exclusions = "";
			$qono_count = count($t->origins);
			for($j = 0; $j < $qono_count; $j++) {
				$origin = $t->origins[$j];
				$url = "geographical_area_view.html?geographical_area_id=" . $origin->geographical_area_id;
				$origins .= "<div><a href='" . $url . "'>" . $origin->description . "</a></div>";
				$qonoe_count = count($origin->exclusions);
				$exclusions = "";
				if ($qonoe_count > 0) {
					$exclusions .= "<p class='exclusions explanatory'><strong>Exclusions:</strong>&nbsp;&nbsp;";
					for($k = 0; $k < $qonoe_count; $k++) {
						$exclusions .= $origin->exclusions[$k]->description;
						if ($k != ($qonoe_count - 1)) {
							$exclusions .= ", ";
						}
					}
					$exclusions .= "</p>";
				}
			}
			$origins .= $exclusions;
			if ($origins == "") {
				$origins = "No origins";
				$rowclass = "dead";
			}
			$url = "http://ec.europa.eu/taxation_customs/dds2/taric/quota_consultation.jsp?Lang=en&Origin=&Code=" . $quota_order_number_id . "&Year=2019&Critical=&Status=&Expand=true";

			# Get definition periods
			$definitions = "";
			foreach ($quota_definitions as $qd) {
				if ($qd->quota_order_number_id == $t->quota_order_number_id) {
					$s		= dm($qd->validity_start_date) . " to " . dm($qd->validity_end_date);
					$pos	= strpos($definitions, $s);
					if ($pos == "") {
						$definitions .= $s . "<br />";
					}
				}
			}
?>
	<tr class="<?=$rowclass?>">
		<td class="govuk-table__cell">
			<a href="quota_order_number_view.html?quota_order_number_id=<?=$quota_order_number_id?>"><?=$quota_order_number_id?></a>
		</td>
		<td class="govuk-table__cell c"><a target="_blank" href="<?=$url?>">EU</a></td>
		<td class="govuk-table__cell"><?=$origins?></td>
		<td class="govuk-table__cell"><?=$measure_types?></td>
		<td class="govuk-table__cell"><?=$validity_start_date?></td>
		<td class="govuk-table__cell"><?=$validity_end_date?></td>
<!--
		<td class="govuk-table__cell"><?=$definitions?></td>
//-->			
		<td class="govuk-table__cell r">
			<form action="quota_order_number_create_edit.html" method="get">
				<input type="hidden" name="phase" value="edit" />
				<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
				<button type="submit" class="govuk-button btn_nomargin")>Edit</button>
			</form>
		</td>
	</tr>
<?php
		}
	} 
?>
</table>
<p class="back_to_top"><a href="#top">Back to top</a></p>
<!--
<h2 id="licensed">Licensed quotas</h2>
<?php
	$sql = "select distinct ordernumber, m.measure_type_id, geographical_area_id, mtd.description as measure_type_description
	from measures m, measure_type_descriptions mtd
	where ordernumber like '094%'
	and m.measure_type_id = mtd.measure_type_id
	and (m.validity_end_date >= '2008-01-01' or validity_end_date is null)
	order by ordernumber, m.measure_type_id, geographical_area_id
	";
	$result = pg_query($conn, $sql);
	$quota_order_numbers = array();
	if ($result) {
?>
<table class="govuk-table" cellspacing="0">
	<thead class="govuk-table__head">
	<tr class="govuk-table__row">
		<th class="govuk-table__header" scope="col">Order number</th>
		<th class="govuk-table__header" scope="col">Measure type</th>
		<th class="govuk-table__header" scope="col">Geographical area</th>
	</tr>
	</thead>
	<tbody>
<?php
		while ($row = pg_fetch_array($result)) {
			$ordernumber				= $row['ordernumber'];
			$measure_type_id			= $row['measure_type_id'];
			$measure_type_description	= $row['measure_type_description'];
			$geographical_area_id		= $row['geographical_area_id'];
?>
	<tr class="govuk-table__row">
		<td class="govuk-table__cell"><?=$ordernumber?></td>
		<td class="govuk-table__cell"><?=$measure_type_id?>&nbsp;<?=$measure_type_description?></td>
		<td class="govuk-table__cell"><?=$geographical_area_id?></td>
	</tr>
<?php
		}
	}
?>
	</tbody>
</table>
<?php		
	
?>
<p class="back_to_top"><a href="#top">Back to top</a></p>
//-->
</div>
<?php
	require ("includes/footer.php")
?>