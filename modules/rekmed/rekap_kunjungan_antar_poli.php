<?
$_TITLE = "Perbandingan Kunjungan Antar Poliklinik";

Class Lap_Jml_Kunjungan_Per_Pelayanan {

	function get_kunjungan_check($val) {
		$val[tgl_periksa_tgl_start] = empty($val[tgl_periksa_tgl_start])?1:$val[tgl_periksa_tgl_start];
		$val[tgl_periksa_bln_start] = empty($val[tgl_periksa_bln_start])?1:$val[tgl_periksa_bln_start];
		$val[tgl_periksa_tgl_end] = empty($val[tgl_periksa_tgl_end])?1:$val[tgl_periksa_tgl_end];
		$val[tgl_periksa_bln_end] = empty($val[tgl_periksa_bln_end])?1:$val[tgl_periksa_bln_end];

		$objResponse = new xajaxResponse;

		$tgl_start = strtotime($val[tgl_periksa_thn_start] . "-" . $val[tgl_periksa_bln_start] . "-" . $val[tgl_periksa_tgl_start]);
		$tgl_end = strtotime($val[tgl_periksa_thn_end] . "-" . $val[tgl_periksa_bln_end] . "-" . $val[tgl_periksa_tgl_end]);

		if(!checkdate($val[tgl_periksa_bln_start], $val[tgl_periksa_tgl_start], $val[tgl_periksa_thn_start])) {
			$objResponse->addAlert("Tanggal Awal Tidak Valid");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_start");
		} elseif(!checkdate($val[tgl_periksa_bln_end], $val[tgl_periksa_tgl_end], $val[tgl_periksa_thn_end])) {
			$objResponse->addAlert("Tanggal Akhir Tidak Valid");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_end");
		} elseif($tgl_start > $tgl_end) {
			$objResponse->addAlert("Tanggal Awal Harus Kurang Dari Tanggal Akhir");
			$objResponse->addScriptCall("fokus", "tgl_periksa_tgl_start");
		} else {
			$objResponse->addScriptCall("xajax_get_kunjungan", $val);
		}
		return $objResponse;
	}

	function get_kunjungan($val) {
		unset($_SESSION[rekmed][rekap_kunjungan_antar_poli]);
		$tgl_start = @date("Y-m-d", @mktime(1,1,1,$val[tgl_periksa_bln_start], $val[tgl_periksa_tgl_start], $val[tgl_periksa_thn_start]));
		$tgl_end = @date("Y-m-d", @mktime(1,1,1,$val[tgl_periksa_bln_end], $val[tgl_periksa_tgl_end], $val[tgl_periksa_thn_end]));
		$title = "Perbandingan Kunjungan Antar Poliklinik";
		
		if($val[tampilkan] == "hari") {
			$selisih = datediff('d', $tgl_start, $tgl_end);
		} elseif($val[tampilkan] == "bulan") {
			$selisih = datediff('m', $tgl_start, $tgl_end);
		} else {
			$selisih = datediff('y', $tgl_start, $tgl_end);
		}
		if($val[tampilkan] == "hari") {
			$tanggal_awal = tanggalIndo($tgl_start, "j F Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "j F Y");
		} elseif($val[tampilkan] == "bulan") {
			$tanggal_awal = tanggalIndo($tgl_start, "F Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "F Y");
		} else {
			$tanggal_awal = tanggalIndo($tgl_start, "Y");
			$tanggal_akhir = tanggalIndo($tgl_end, "Y");
		}

		$title .= "\nPeriode " . $tanggal_awal . " s.d. " . $tanggal_akhir;

		$arr_tgl_start = explode("-",$tgl_start);
		$tgl_start_tgl = $arr_tgl_start[2];
		$tgl_start_bln = $arr_tgl_start[1];
		$tgl_start_thn = $arr_tgl_start[0];

		$arr_tgl_end = explode("-",$tgl_end);
		$tgl_end_tgl = $arr_tgl_end[2];
		$tgl_end_bln = $arr_tgl_end[1];
		$tgl_end_thn = $arr_tgl_end[0];

		$objResponse = new xajaxResponse();
		$kon = new Konek;
		$kon->sql = "SELECT id, nama, hari_buka FROM pelayanan WHERE jenis = 'RAWAT JALAN'";
		$kon->execute();
		$poli = $kon->getAll();
		
		for($i=0;$i<=$selisih;$i++) {
			if($val[tampilkan] == "hari") {
				$tgl[$i] = @date("Y-m-d", @mktime(1, 1, 1, $tgl_start_bln, ($i+$tgl_start_tgl), $tgl_start_thn));
				$data[$i][tgl_periksa] = tanggalIndo($tgl[$i], "j M\nY");
				$_SESSION[rekmed][rekap_kunjungan_antar_poli][label_tick][$i] = @date("j", @mktime(1, 1, 1,$tgl_start_bln, ($tgl_start_tgl+$i), $tgl_start_thn));
				$label_x[$i] = @date("M Y", @mktime(1, 1, 1, $tgl_start_bln, ($tgl_start_tgl+$i), $tgl_start_thn));
				$where = " AND DATE(kk.tgl_periksa) = '" . $tgl[$i] . "' ";
			} elseif($val[tampilkan] == "bulan") {
				$tgl[$i] = @date("Y-m-d", @mktime(1, 1, 1, ($tgl_start_bln+$i), 1, $tgl_start_thn));
				$data[$i][tgl_periksa] = tanggalIndo($tgl[$i], "M\nY");
				$_SESSION[rekmed][rekap_kunjungan_antar_poli][label_tick][$i] = @date("M y", @mktime(1, 1, 1, ($tgl_start_bln+$i), 1, $tgl_start_thn));
				$label_x[$i] = @date("Y", @mktime(1, 1, 1, ($tgl_start_bln+$i), 1, $tgl_start_thn));
				$where = " AND EXTRACT(YEAR_MONTH FROM kk.tgl_periksa) = EXTRACT(YEAR_MONTH FROM '" . $tgl[$i] . "') ";
			} else {
				$tgl[$i] = @date("Y-m-d", @mktime(1, 1, 1, 1, 1, ($tgl_start_thn + $i)));
				$data[$i][tgl_periksa] = tanggalIndo($tgl[$i], "Y");
				$_SESSION[rekmed][rekap_kunjungan_antar_poli][label_tick][$i] = @date("Y", @mktime(1, 1, 1, 1, 1, ($tgl_start_thn+$i)));
				$label_x[$i] = "Tahun";
				$where = " AND YEAR(kk.tgl_periksa) = YEAR('" . $tgl[$i] . "') ";
			}
			
			for($s=0;$s<sizeof($poli);$s++) {
				$sql = "
					SELECT
						COUNT(kk.id) as jml,
						DATE(kk.tgl_periksa) as tgl_periksa
					FROM
						kunjungan_kamar kk 
						JOIN kamar kmr ON (kmr.id = kk.kamar_id)
						JOIN pelayanan pel ON (pel.id = kmr.pelayanan_id)
					WHERE
						pel.id = '".$poli[$s][id]."'
						$where
					GROUP BY
						pel.id
					";
				$kon->sql = $sql;
				$kon->execute();
				$baru[$s][$i] = $kon->getOne();
				if(!$baru[$s][$i][jml]) $baru[$s][$i][jml] = 0;
				
				$_SESSION[rekmed][rekap_kunjungan_antar_poli][nama][$s] = $poli[$s][nama];
				$_SESSION[rekmed][rekap_kunjungan_antar_poli][jml][$s][$i] = $baru[$s][$i][jml];
			}
			
			$_SESSION[rekmed][rekap_kunjungan_antar_poli][tgl_periksa][$i] = $data[$i][tgl_periksa];
		}
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][title] = $title . "\nPeriode " . tanggalIndo($tgl_start, "j F Y") . " s.d. " . tanggalIndo($tgl_end, "j F Y");
		$labelx = @array_unique($label_x);
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][label_x] = @implode(", ",$labelx);

		$table = new Table;
		$table->scroll = false;
		$table->Th[0][] = "No";
		$table->Th[0][] = "Poliklinik";
		$table->Th[1][] = "1";
		$table->Th[1][] = "2";
		for($i=0;$i<=$selisih;$i++) {
			$table->Th[0][] = $data[$i][tgl_periksa];
			$table->Th[1][] = ($i+3);
		}
		$table->Th[0][] = "Jumlah";
		$table->Th[1][] = $i+3;

		$_SESSION[rekmed][rekap_kunjungan_antar_poli][th_0] = $table->Th[0];
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][th_1] = $table->Th[1];

		//isinya
		for($s=0;$s<sizeof($poli);$s++) { //menurun
			$table->Row[$s][] = $s+1;
			$table->Row[$s][] = $poli[$s][nama];
			for($i=0;$i<sizeof($baru[$s]);$i++) { //mendatar
				$table->Row[$s][] = $baru[$s][$i][jml];
				$jml_ver[$i] += $baru[$s][$i][jml];
				$jml_hor[$s] += $baru[$s][$i][jml];
			}
			$table->Row[$s][] = $jml_hor[$s];
			$total += $jml_hor[$s];
			$_SESSION[rekmed][rekap_kunjungan_antar_poli][row][$s] = $table->Row[$s];
		}

		//baris terakhir		
		$x = sizeof($poli);
		$table->Row[$x][] = "&nbsp;";
		$table->Row[$x][] = "<b>Jumlah</b>";
		for($i=0;$i<sizeof($baru[$x-1]);$i++) {
			$table->Row[$x][] = $jml_ver[$i];
		}
		$table->Row[$x][] = $total;
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][last_row] = $table->Row[$x];
		$ret = $table->build();
		
		$objResponse->addAssign("list_data", "innerHTML", $ret);
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][graph] = "<img src=\"" . URL . "rekmed/rekap_kunjungan_antar_poli_graph_bar/?md5=".md5(date("Ymdhis"))."\" alt=\"Kunjungan\" />";
		$_SESSION[rekmed][rekap_kunjungan_antar_poli][title] = $title;
		$objResponse->addAssign("title", "innerHTML", nl2br($_SESSION[rekmed][rekap_kunjungan_antar_poli][title]));
		$objResponse->addAssign("graph", "innerHTML", $_SESSION[rekmed][rekap_kunjungan_antar_poli][graph]);
		return $objResponse;
	}

}

$_xajax->registerFunction(array("get_kunjungan", "Lap_Jml_Kunjungan_Per_Pelayanan", "get_kunjungan"));
$_xajax->registerFunction(array("get_kunjungan_check", "Lap_Jml_Kunjungan_Per_Pelayanan", "get_kunjungan_check"));
?>