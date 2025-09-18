<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$draw     = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start     = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$fullname = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$p = new paging;
$sql = "SELECT a.*, CONCAT(b.nama_mobil,' - ', b.plat_mobil) as nama_mobil, CONCAT(e.nama_transportir,' - ', d.nomor_plat) as nama_truck, c.nama_terminal, c.tanki_terminal FROM pro_pengisian_solar_mobil_opr a LEFT JOIN pro_master_mobil b ON a.id_mobil=b.id_mobil LEFT JOIN pro_master_terminal c ON a.id_terminal=c.id_master LEFT JOIN pro_master_transportir_mobil d ON a.id_truck=d.id_master LEFT JOIN pro_master_transportir e ON d.id_transportir=e.id_master WHERE a.id_wilayah = '" . $seswil . "'";

if ($q1 != "") {
    $sql .= " and (upper(a.nomor) like '%" . strtoupper($q1) . "%' or upper(a.driver) like '%" . strtoupper($q1) . "%')";
}

if ($q2 != "") {
    $sql .= " and a.disposisi = '" . $q2 . "'";
}

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id DESC limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;

        $pathfile    = BASE_URL . '/files/uploaded_user/file_pengisian_solar_mobil_opr/' . $data['lampiran'];
        $pathfileRealisasi = BASE_URL . '/files/uploaded_user/file_pengisian_solar_mobil_opr/' . $data['lampiran_realisasi'];
        $linkEdit    = BASE_URL_CLIENT . '/add_pengisian_solar_mobil.php?' . paramEncrypt('id=' . $data['id']);
        $pathCetak =  ACTION_CLIENT . '/cetak_voucher_bbm.php?' . paramEncrypt('id=' . $data['id'] . '&kategori=pengajuan_awal');
        $pathCetakRealisasi =  ACTION_CLIENT . '/cetak_voucher_bbm.php?' . paramEncrypt('id=' . $data['id'] . '&kategori=realisasi');

        if ($data['disposisi'] == 0) {
            if ($id_role == "24") {
                $linkHapus    = paramEncrypt("pengisian_solar_mobil#|#" . $data['id']);
                $btnHapus = '<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
                $btnVerif = "";
                $linkCancel = paramEncrypt("pengisian_solar_mobil#|#" . $data['id']);
                $btnCancel = '<a class="margin-sm btn btn-action btn-danger " title="Cancel" data-param-idx="' . $linkCancel . '" data-action="cancelGrid"><i class="fas fa-window-close"></i></a>';
                $btnEdit = '<a class="margin-sm btn btn-action btn-primary" title="Edit" href="' . $linkEdit . '"><i class="fa fa-edit"></i></a>';
            } elseif ($id_role == "10") {
                $linkVerif    = paramEncrypt("verif_pengisian_solar#|#" . $data['id']);
                $btnVerif = '<a class="margin-sm btn btn-action btn-primary " title="Verifikasi" data-param-idx="' . $linkVerif . '" data-action="verifGrid"><i class="fas fa-check-circle"></i></a>';
                $btnHapus = "";
                $btnCancel = "";
                $btnEdit = "";
            }
        } else {
            $status .= "<br><small>" . $data['admin_pic'] . "</small>";
            $status .= "<br><small>" . tgl_indo($data['date_admin']) . " " . date("H:i:s", strtotime($data['date_admin'])) . "</small>";

            $btnHapus = "";
            $btnVerif = "";
            $btnEdit = "";

            if ($id_role == "10") {
                if ($data['disposisi'] == 2) {
                    $btnCetak = "";
                } else {
                    $btnCetak = "<a target='_blank' href='" . $pathCetak . "' class='margin-sm btn btn-action btn-danger' title='Cetak Voucher'><i class='fas fa-file-pdf'></i></a>";
                    $btnRealisasi = "";
                }
            } else {
                if ($data['tgl_realisasi'] != NULL) {
                    $btnRealisasi = "";
                    $btnCancel = "";
                    $btnCetak = "<a target='_blank' href='" . $pathCetak . "' class='margin-sm btn btn-action btn-danger' title='Cetak Voucher'><i class='fas fa-file-pdf'></i></a>";
                } else {
                    if ($data['disposisi'] == 2) {
                        $btnCancel = "";
                        $btnRealisasi = "";
                        $btnCetak = "";
                    } else {
                        $btnCetak = "<a target='_blank' href='" . $pathCetak . "' class='margin-sm btn btn-action btn-danger' title='Cetak Voucher'><i class='fas fa-file-pdf'></i></a>";
                        $linkCancel = paramEncrypt("pengisian_solar_mobil#|#" . $data['id']);
                        $btnCancel = '<a class="margin-sm btn btn-action btn-danger " title="Cancel" data-param-idx="' . $linkCancel . '" data-action="cancelGrid"><i class="fas fa-window-close"></i></a>';
                        $btnRealisasi = "<button type='button' class='btn btn-primary btn-sm btnRealisasi' title='Realisasi' data-param-idx='" . paramEncrypt($data['id']) . "' data-param-vol='" . $data['volume'] . "' data-param-driver='" . $data['driver'] . "' data-param-ket='" . $data['keterangan'] . "'> Realisasi</button>";
                    }
                }
            }
        }

        if ($data['lampiran'] == NULL) {
            $btnFile = "";
        } else {
            $btnFile = "<a target='_blank' href='" . $pathfile . "' class='margin-sm btn btn-action btn-info' title='Lampiran'><i class='fas fa-paperclip'></i></a>";
        }

        $status    = ($data["disposisi"] == 1) ? "<span class='badge' style='background-color: #28a745;'>Terverifikasi</span>" : "<span class='badge' style='background-color: #ffc107;'>Verifikasi admin finance</span>";

        if ($data['disposisi'] == 1) {
            $status = "<span class='badge' style='background-color: #28a745;'>Terverifikasi</span>";
        } elseif ($data['disposisi'] == 2) {
            $status = "<span class='badge' style='background-color: #e70404ff;'>Cancel</span>";
        } else {
            $status = "<span class='badge' style='background-color: #ffc107;'>Verifikasi admin finance</span>";
        }

        if ($data['is_admin_realisasi'] == 1) {
            $status_realisasi = "<span class='badge' style='background-color: #28a745;'>Terverifikasi</span>";
            $btnFileRealisasi = "<a target='_blank' href='" . $pathfileRealisasi . "' class='margin-sm btn btn-action btn-info' title='Lampiran'><i class='fas fa-paperclip'></i></a>";
            $btnCetakRealisasi = "<a target='_blank' href='" . $pathCetakRealisasi . "' class='margin-sm btn btn-action btn-danger' title='Cetak Voucher Realisasi'><i class='fas fa-file-pdf'></i></a>";
            $textBtnRealisasi = "Lampiran : $btnFileRealisasi $btnCetakRealisasi";
        } else {
            if ($data['disposisi'] != 2) {
                $status_realisasi = "<span class='badge' style='background-color: #ffc107;'>Verifikasi admin finance</span>";
                $btnFileRealisasi = "<a target='_blank' href='" . $pathfileRealisasi . "' class='margin-sm btn btn-action btn-info' title='Lampiran'><i class='fas fa-paperclip'></i></a>";
                if ($data['tgl_realisasi'] != NULL) {
                    if ($id_role == "10") {
                        $linkVerifRealisasi    = paramEncrypt("verif_realisasi_pengisian_solar#|#" . $data['id']);
                        $btnVerifRealisasi = '<a class="margin-sm btn btn-action btn-primary " title="Verifikasi" data-param-idx="' . $linkVerifRealisasi . '" data-action="verifRealisasiGrid"><i class="fas fa-check-circle"></i></a>';

                        $textBtnRealisasi = "Lampiran : $btnFileRealisasi $btnVerifRealisasi";
                    } else {
                        $btnVerifRealisasi = "";
                        $textBtnRealisasi = "Lampiran : $btnFileRealisasi";
                    }
                } else {
                    $status_realisasi = "";
                    $textBtnRealisasi = "";
                }
            } else {
                $status_realisasi = "";
                $textBtnRealisasi = "";
            }
        }

        // Ubah string ke float
        $volume = (float)$data['volume'];
        $volume2 = (float)$data['volume_realisasi'];

        // Jika angka adalah bilangan bulat (tanpa desimal)
        if (floor($volume) == $volume) {
            $volume = number_format($volume, 0, '.', ','); // tampilkan tanpa desimal
        } else {
            $volume = number_format($volume, 4, '.', ','); // tampilkan 4 angka di belakang koma
        }

        if ($volume2 == 0) {
            $volume_realisasi = 0;
        } else {
            if (floor($volume2) == $volume2) {
                $volume_realisasi = number_format($volume2, 0, '.', ','); // tampilkan tanpa desimal
            } else {
                $volume_realisasi = number_format($volume2, 4, '.', ','); // tampilkan 4 angka di belakang koma
            }
        }


        if ($data['nomor'] == NULL) {
            $no_voucher = "-";
        } else {
            $no_voucher = $data['nomor'];
        }

        if ($data['nama_mobil'] == NULL) {
            $unit = $data['nama_truck'];
        } else {
            $unit = $data['nama_mobil'];
        }

        $content .= '
		<tr>
			<td class="text-center">' . $count . '</td>
			<td class="text-center">' . $no_voucher . '</td>
			<td class="text-left">
			Driver : ' . strtoupper($data['driver']) . '
			<br>
			Tujuan : ' . strtoupper($data['tujuan']) . '
			<hr>
			' . $unit . '
			</td>
			<td class="text-left">
			' . $data['nama_terminal'] . ' - ' . $data['tanki_terminal'] . '
			<br>
			' . $volume . ' Liter
			<hr>
			Realisasi : ' . $volume_realisasi . ' Liter
			<br>
			' . $btnRealisasi . '
			' . $textBtnRealisasi . '
			<br>
			' . $status_realisasi . '
			</td>
			<td class="text-left">' . $data['keterangan'] . '</td>
			<td class="text-center">' . $status . '</td>
			<td class="text-center">
			' . $btnCancel . '
			' . $btnVerif . '
			' . $btnFile . '
			' . $btnCetak . '
			' . $btnEdit . '
			</td>
		</tr>';
    }
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);
