<?php
    $title = "Geographical areas";
    require ("includes/db.php");
    require ("includes/header.php");
    
    $regulation_group_id    = get_querystring("regulation_group_id");
    $geographical_area_text = get_querystring("geographical_area_text");
    $geography_scope        = get_querystring("geography_scope");
    $area_code              = get_querystring("area_code");
    if ($area_code == "") {
        $area_code = "0,1,2";
    }
    $area_codes = explode(",", $area_code);
    $area_code = "";
    foreach($area_codes as $code){
        $area_code .= "'" . $code . "',";
    }
    $area_code = rtrim($area_code, ",");
    
    if ($geography_scope == "") {
        $geography_scope = "all";
    }
?>

<!-- Start breadcrumbs //-->
<div id="wrapper" class="direction-ltr">
    <div class="gem-c-breadcrumbs govuk-breadcrumbs " data-module="track-click">
    <ol class="govuk-breadcrumbs__list">
        <li class="govuk-breadcrumbs__list-item">
            <a class="govuk-breadcrumbs__link" href="/">Main menu</a>
        </li>
        <li class="govuk-breadcrumbs__list-item">
            <a class="govuk-breadcrumbs__link" href="/regulations.html">Geographical areas</a>
        </li>
<?php
    if ($regulation_group_id != "") {
?>
        <li class="govuk-breadcrumbs__list-item">
            <?=$regulation_group_id?>
        </li>
<?php
    }
?>        
    </ol>
    </div>
    <!-- End breadcrumbs //-->
    <div class="app-content__header">
        <h1 class="govuk-heading-xl">Geographical areas</h1>
    </div>

    <form action="/actions/geographical_area_actions.html" method="get" class="inline_form">
        <h3>Filter results</h3>
        <input type="hidden" name="phase" id="phase" value="filter_geography" />
        <div class="column-one-third" style="width:320px">
            <div class="govuk-form-group">
                <fieldset class="govuk-fieldset" aria-describedby="base_regulation_hint" role="group">
                    <span id="base_regulation_hint" class="govuk-hint">Search geographical area IDs - enter free text</span>
                    <div class="govuk-date-input" id="measure_start">
                        <div class="govuk-date-input__item">
                            <div class="govuk-form-group" style="padding:0px;margin:0px">
                                <input value="<?=$geographical_area_text?>" class="govuk-input govuk-date-input__input govuk-input--width-16" id="geographical_area_text" maxlength="100" style="width:300px" name="geographical_area_text" type="text">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>

        </div>
        
        <div class="column-one-third">
            <div class="govuk-form-group" style="padding:0px;margin:0px">
                <button type="submit" class="govuk-button" style="margin-top:36px">Search</button>
            </div>
        </div>
        <div class="clearer"><!--&nbsp;//--></div>



        <details class="govuk-details" data-module="govuk-details">
            <summary class="govuk-details__summary">
                <span class="govuk-details__summary-text medium">Advanced filters</span>
            </summary>
            <div class="govuk-details__text">
                <legend class="govuk-fieldset__legend govuk-fieldset__legend--xl" style="width:100% !important;">
                    <h1 class="govuk-fieldset__heading large" style="min-width:90% !important;">
                        Select types of geographical area
                    </h1>
                </legend>
                <div style="width:40%;float:left">
                    <div class="govuk-radios govuk-radios--inline" style="margin-bottom:0.5em">
                        <div class="govuk-radios__item break">
                            <input <?=get_checked($geography_scope, "all")?> type="radio" class="govuk-radios__input" name="geography_scope" id="geography_scope_all" value="all" />
                            <label class="govuk-label govuk-radios__label" for="geography_scope_all">Show all geographical areas</label>
                        </div>
                    </div>
                    <div class="govuk-radios govuk-radios--inline">
                        <div class="govuk-radios__item break">
                            <input <?=get_checked($geography_scope, "current")?> type="radio" class="govuk-radios__input" name="geography_scope" id="geography_scope_current" value="current" />
                            <label class="govuk-label govuk-radios__label" for="geography_scope_current">Only show current geographical areas</label>
                        </div>
                    </div>
                </div>
                <div style="width:55%;float:left;">
                    
                    <div class="govuk-form-group">
                        <fieldset class="govuk-fieldset" aria-describedby="geographical_area_code-hint">
                            <div class="govuk-checkboxes">
                                <div class="govuk-checkboxes__item">
                                    <input <?=get_checked_array($area_codes, 0)?> class="govuk-checkboxes__input" id="geographical_area_code-0" name="geographical_area_code[]" type="checkbox" value="0">
                                    <label class="govuk-label govuk-checkboxes__label" for="geographical_area_code-0">
                                    Countries
                                    </label>
                                </div>
                                <div class="govuk-checkboxes__item">
                                    <input <?=get_checked_array($area_codes, 2)?> class="govuk-checkboxes__input" id="geographical_area_code-2" name="geographical_area_code[]" type="checkbox" value="2">
                                    <label class="govuk-label govuk-checkboxes__label" for="geographical_area_code-2">
                                    Regions
                                    </label>
                                </div>
                                <div class="govuk-checkboxes__item">
                                    <input <?=get_checked_array($area_codes, 1)?> class="govuk-checkboxes__input" id="geographical_area_code-1" name="geographical_area_code[]" type="checkbox" value="1">
                                    <label class="govuk-label govuk-checkboxes__label" for="geographical_area_code-1">
                                    Country groups
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>                

                </div>
                <div class="clearer"><!--&nbsp;//--></div>

            </div>
        </details>


        
    </form>
<?php
    $clause = "";
    $offset         = intval($pagesize) * ($page - 1);
    $limit_clause   = " LIMIT " . $pagesize . " OFFSET " . $offset;

    if ($regulation_group_id != "") {
        $clause .= "WHERE b.regulation_group_id = '" . $regulation_group_id . "'";
    }
    $geo_clause = "";
    if ($geographical_area_text != "") {
        $geo_clause = " (LOWER(geographical_area_id) LIKE '%" . strtolower($geographical_area_text) . "%' OR LOWER(description) LIKE '%" . strtolower($geographical_area_text) . "%')";
    }

    $countsql = "SELECT COUNT(DISTINCT geographical_area_id) FROM ml.ml_geographical_areas ";
    if ($geo_clause != "") {
        $countsql .= " WHERE " . $geo_clause;
    }
    $result = pg_query($conn, $countsql);
    if ($result) {
        $row = pg_fetch_row($result);
        $result_count = $row[0];
        $page_count = ceil($result_count / $pagesize);
    }

    $where_added = False;
    $sql = "SELECT geographical_area_sid, geographical_area_id, description, geographical_code,
    validity_start_date, validity_end_date FROM ml.ml_geographical_areas ";
    if ($geo_clause != "") {
        $sql .= " WHERE " . $geo_clause;
        $where_added = True;
    }
    if ($geography_scope == "current") {
        if ($where_added == True) {
            $sql .= " AND ";
        } else {
            $sql .= " WHERE ";
        }
        $sql .= " validity_end_date IS NULL ";
        $where_added = True;
    }

    // Area codes
    if ($where_added == True) {
        $sql .= " AND ";
    } else {
        $sql .= " WHERE ";
    }
    $sql .= " geographical_code in (" . $area_code . ") ";
    $where_added = True;
    


    $sql .= " ORDER BY 2";
    $sql .= $limit_clause;
    //echo ($sql);
    $result = pg_query($conn, $sql);
	if (($result) && pg_num_rows($result) > 0) {
?>

<table class="govuk-table" cellspacing="0">
    <tr class="govuk-table__row">
        <th class="govuk-table__header" style="width:10%">Area ID</th>
        <th class="govuk-table__header c" style="width:10%">Area SID</th>
        <th class="govuk-table__header" style="width:46%">Description</th>
        <th class="govuk-table__header c" style="width:10%">Geographical code</th>
        <th class="govuk-table__header" style="width:12%">Start date</th>
        <th class="govuk-table__header" style="width:12%">End date</th>
    </tr>
<?php    
		while ($row = pg_fetch_array($result)) {
            $geographical_area_id       = $row['geographical_area_id'];
            $geographical_area_sid      = $row['geographical_area_sid'];
            $description                = $row['description'];
            $geographical_code          = $row['geographical_code'];
            $validity_start_date        = short_date($row['validity_start_date']);
            $validity_end_date          = short_date($row['validity_end_date']);
            $rowclass                    = rowclass($validity_start_date, $validity_end_date);
?>
    <tr class="<?=$rowclass?>">
        <td class="govuk-table__cell"><a href="/geographical_area_view.html?geographical_area_id=<?=$geographical_area_id?>"><?=$geographical_area_id?></a></td>
        <td class="govuk-table__cell c"><?=$geographical_area_sid?></td>
        <td class="govuk-table__cell"><?=$description?></td>
        <td class="govuk-table__cell c"><?=$geographical_code?><br /><span class="explanatory"><?=geographical_code($geographical_code)?></span></td>
        <td class="govuk-table__cell"><?=$validity_start_date?></td>
        <td class="govuk-table__cell"><?=$validity_end_date?></td>
    </tr>
<?php            
		}
	} else {
        echo ("<p>No matching geographical areas</p>");
    }
?>
</table>
<?php
    $url = "/geographical_areas.php?";
    for ($i = 1; $i <= $page_count; $i++) {
        if (intval($i) == intval($page)) {
            $class = " selected";
        } else {
            $class = "";
        }
        $url2 = $url . "&page=" . $i;
        echo ("<span class='paging " . $class ."'><a href='" . $url2 . "'>" . $i . "</a></span>");
    }
?>
<div class="clearer"><!--&nbsp;//--></div>
</div>
<?php
    require ("includes/footer.php")
?>