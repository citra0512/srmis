<?php

	$my = date("m/Y");
	$thn= date("Y");
	$bln = date("m") + 0;
	$my = date ("m/Y");
	$bulan = "b".$bln;
	$rbulan = "rb".$bln;
	$lt = "lt".$bln;
	
	$query = mysql_query ("SELECT * FROM safety_stock WHERE tahun = '$thn'");
	while ($result = mysql_fetch_array($query))
	{
		$q_ms_b = mysql_query ("SELECT * FROM ms_barang WHERE id = '$result[barang_id]'");
		$r_ms_b = mysql_fetch_array ($q_ms_b);
		
		$q_adc = mysql_query ("SELECT * FROM adc WHERE barang_id = '$r_ms_b[kd_barang]' AND tahun='$thn'");
		$r_adc = mysql_fetch_array ($q_adc);
		if ($r_adc)
		{
			$safety_stock = $r_adc[$rbulan];
		}
		else
		{
			$safety_stock = 1;
		}
		$lead_time = $result[$lt];
		$min_stock_lv = $safety_stock + $lead_time;
		$max_stock_lv = $min_stock_lv + $lead_time;
		//echo $max_stock_lv . "<br>";
		
		$q_lv = mysql_query ("SELECT * FROM stok_level WHERE barang_id = '$result[barang_id]'");
		$r_lv = mysql_fetch_array ($q_lv);
		if (!$r_lv)
		{
			$q_masuk = "INSERT INTO stok_level (barang_id,min_stock_lv, max_stock_lv) VALUES ('$result[barang_id]', '$min_stock_lv', '$max_stock_lv')";
			$r_masuk = mysql_query ($q_masuk);
		}
		else
		{
			$q_masuk = mysql_query ("UPDATE stok_level SET min_stock_lv = '$min_stock_lv',
														   max_stock_lv = '$max_stock_lv' WHERE barang_id = '$result[barang_id]'");
		}
		
	}

?>