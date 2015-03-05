<?php

    $config = include('../config/autoload/user.global.php');	
	
	$mysqli = new mysqli('localhost', $config['db']['username'], $config['db']['password'], 'hirdetek');

    $mysqli->set_charset("utf8");
	
    $regiok = array();

    $total_items = 0;

    if ($result = $mysqli->query("SELECT * FROM regio ORDER BY `order`")) {
        $total_items = $result->num_rows;
        while($row = $result->fetch_array(MYSQL_ASSOC)) $regiok[] = $row;
        $result->close();
    }

    $regiok = array("_embedded" => array("regio" => $regiok), "page_count" => 1, "page_size" => 1000, "total_items" => $total_items);

    $rovatok = array();

    $total_items = 0;

    if ($result = $mysqli->query("SELECT * FROM rovat ORDER BY `order`")) {
        $total_items = $result->num_rows;
        while($row = $result->fetch_array(MYSQL_ASSOC)) $rovatok[] = $row;
        $result->close();
    }

    $rovatok = array("_embedded" => array("rovatok" => $rovatok), "page_count" => 1, "page_size" => 1000, "total_items" => $total_items);

    $hirdetesek = array();

    $page_size   = 25;
    $total_items =  0;
    $page_count  =  0;

    if ($result = $mysqli->query(
        "SELECT h.*,
                i.id  as image_id, i.created as image_created, i.name as image_name,
                r.id  as r_rovat_id, r.nev   as r_rovat_nev, r.slug   as r_rovat_slug,
                pr.id as p_rovat_id, pr.nev  as p_rovat_nev, pr.slug  as p_rovat_slug,
                g.id  as g_regio_id, g.nev   as g_regio_nev, g.slug   as g_regio_slug,
                pg.id as p_regio_id, pg.nev  as p_regio_nev, pg.slug  as p_regio_slug
         FROM hirdetes h
         LEFT JOIN images i ON i.ad_id = h.id AND i.sorrend=1
         LEFT JOIN rovat r ON r.id = h.rovat
         LEFT JOIN rovat pr ON pr.id = r.parent
         LEFT JOIN regio g ON g.id = h.regio
         LEFT JOIN regio pg ON pg.id = g.parent
         ORDER BY h.feladas DESC"
    )) {
        $total_items = $result->num_rows;
        $page_count = (int) ceil($total_items / $page_size);
        $i = 0;
        while(($row = $result->fetch_array(MYSQL_ASSOC)) && ($i < 25)) {
            $hirdetesek[] = $row;
            $i++;
        }

        $result->close();
    }

    $hirdetesek = array("_embedded" => array("hirdetes" => $hirdetesek), "page_count" => $page_count, "page_size" => $page_size, "total_items" => $total_items);

    $mysqli->close();

?>

<div ng-cloak preload-resource='{"regiok": <?php echo json_encode($regiok) ?>, "rovatok": <?php echo json_encode($rovatok) ?>, "hirdetesek": <?php echo json_encode($hirdetesek) ?>}'></div>
