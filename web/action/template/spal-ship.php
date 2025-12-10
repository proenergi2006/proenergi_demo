
<style>
    body {
        /* font-family: "Times New Roman", Times, serif; */
        font-size: 12px;
        color: #333;
        text-align: justify;
    }

    .title-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .title-text {
        font-size: 18px;
        font-weight: bold;
    }

    .title-line {
        width: 80px;
        height: 2px;
        background: #222;
        margin: 6px auto 0 auto;
    }

    .info-box {
        border: 1px solid #404040ff;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 15px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        /* margin-bottom: 10px; */
    }

    .col {
        width: 48%;
        margin-bottom: 10px;
    }

    .label {
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        line-height:2;
    }
    
</style>

<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />

<?php 
$pic	= json_decode($data['att_suplier'], true);
$loading_date='';
if(date("m",strtotime($res[0]['etl_date_first']) == date("m",strtotime($res[0]['etl_date_last'])))){
    if(date("d",strtotime($res[0]['etl_date_first'])) == date("d",strtotime($res[0]['etl_date_last']))){
        $loading_date=tgl_indo($res[0]['etl_date_first']);
    }else{
        $loading_date=date("d",strtotime($res[0]['etl_date_first']))." - ".tgl_indo(($res[0]['etl_date_last']));
    }
}else{
    $loading_date=($res[0]['etl_date_first'])."-".tgl_indo($res[0]['etl_date_last']);
}
?>
<div class="container">
    <h5 style="text-align:center; font-size:16px; margin:5px 0;">SURAT PERJANJIAN ANGKUTAN LAUT (SPAL)</h5>
    <h5 style="text-align:center; font-size:16px; margin:5px 0;"><?php echo $res[0]['nama_suplier']; ?></h5>
    <h5 style="text-align:center; font-size:16px; margin:5px 0;">DAN</h5>
    <h5 style="text-align:center; font-size:16px; margin:5px 0;">PT. PRO ENERGI</h5>
    <h5 style="text-align:center; font-size:14px; margin:5px 0;"><?php echo $res[0]['nomor_req']; ?></h5>

    <p style="padding-top: 10px;">SPAL ini dibuat dan ditandatangani tanggal 13 November 2025 oleh dan antara : </p>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
        <tr>
            <td width="20%">Nama</td>
            <td width="2%">:</td>
            <td><?php echo $pic; ?></td>
        </tr>
        <tr>
            <td width="20%">Jabatan</td>
            <td width="2%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Perusahaan</td>
            <td width="2%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Alamat</td>
            <td width="2%">:</td>
            <td></td>
        </tr>
    </table>

    <p>Bertindak selaku pemilik dan operator kapal disebut <b>PIHAK PERTAMA</b></p>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
        <tr>
            <td width="20%">Nama</td>
            <td width="2%" >:</td>
            <td>Vica Krisdianatha</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td width="2%" >:</td>
            <td>Direktur Utama</td>
        </tr>
        <tr>
            <td>Perusahaan</td>
            <td width="2%" >:</td>
            <td>PT. Pro Energi</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Alamat</td>
            <td  width="2%" style="vertical-align: top;">:</td>
            <td>
                <div>
                    <p style="padding: 2px 0;">Gedung Graha Irama Lantai 6 Unit G</p>
                    <p style="padding: 2px 0;">Jl. HR. Rasuna Said Blok X1, Kav 1-2, Kuningan, Jakarta, 12950</p>
                </div>
            </td>
        </tr>
    </table>
    <p>Bertindak selaku pemilik cargo disebut <b>PIHAK KEDUA</b></p>
    <p>Untuk selanjutnya secara bersama-sama <b>PIHAK PERTAMA</b> dan <b>PIHAK KEDUA </b>disebut dengan <b>PARA PIHAK</b></p>
    <p><b>PARA PIHAK</b> telah sepakat untuk mengikat dalam suatu perjanjian angkutan laut dengan syarat-syarat sebagai berikut :</p>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
        <tr>
            <td width="20%">Nama Kapal</td>
            <td  width="2%">:</td>
            <td><?= $res[0]['nama_kapal']." & ". $res[0]['tipe_kapal']; ?></td>
        </tr>
        <tr>
            <td width="20%">Jenis Minyak</td>
            <td width="2%">:</td>
            <td>Minyak Solar(B40)</td>
        </tr>
        <tr>
            <td width="20%">Muatan Kapal</td>
            <td width="2%">:</td>
            <td><?php echo number_format($res[0]['quantity']); ?> Liter’15 (GSV)</td>
        </tr>
        <tr>
            <td width="20%">Pelabuhan Muat</td>
            <td width="2%">:</td>
            <td><?php echo $res[0]['asal_angkut']; ?></td>
        </tr>
        <tr>
            <td width="20%">Pelabuhan Bongkar</td>
            <td width="2%">:</td>
            <td><?php echo $res[0]['tujuan_angkut']; ?></td>
        </tr>
        <tr>
            <td width="20%">Ongkos Angkut</td>
            <td width="2%">:</td>
            <td>Rp <?php echo number_format($res[0]['freight']); ?> /liter (diluar PPN 11%) dan termasuk PPh</td>
        </tr>
        <tr>
            <td width="20%">Transport Biaya <i>Demurrage</i></td>
            <td width="2%">:</td>
            <td>Rp <?php echo number_format($res[0]['demurrage']); ?> (24 Jam) (<i>pdpr; per day pro rata</i>)</td>
        </tr>
        <tr>
            <td width="20%" style="vertical-align: top;">Waktu Muat & Bongkar</td>
            <td width="2%" style="vertical-align: top;">:</td>
            <td><p>Total 3 hari/ 72 Jam</p>
                <p>(dihitung secara akumulasi sejak kapal sandar dan berakhir sejak hose dilepas baik di Pelabuhan muat dan Pelabuhan bongkar)</p>
                </td>
        </tr>
        <tr>
            <td width="20%">Tanggal Muat (<i>laycan</i>)</td>
            <td width="2%">:</td>
            <td><?php echo $loading_date; ?></td>
        </tr>
        <tr>
            <td width="20%">Toleransi Susut</td>
            <td width="2%">:</td>
            <td>R2 (<i>Ship Figure After Loading vs Ship Figure Before Discharge</i>) <?php echo $res[0]['loss_tolerance']; ?> liter ’15 (GSV).</td>
        </tr>
        <tr>
            <td width="20%">Asuransi Kapal</td>
            <td width="2%">:</td>
            <td>PIHAK PERTAMA</td>
        </tr>
        <tr>
            <td width="20%">Asuransi Cargo</td>
            <td width="2%">:</td>
            <td>PIHAK KEDUA</td>
        </tr>
        <tr>
            <td width="20%" style="vertical-align: top;">Pembayaran</td>
            <td width="2%" style="vertical-align: top;">:</td>
            <td><p>100% - 14 hari kerja setelah bongkar dan dokumen invoice diterima lengkap </p>
            <p>dan nilai Invoice Basis Bill of Loading dalam liter ’15 (GSV)</p>
            </td>
        </tr>
        <tr>
            <td width="20%">Transfer ke</td>
            <td width="2%">:</td>
            <td>Sesuai dengan Invoice</td>
        </tr>
        <tr>
            <td width="20%">Penyelesaian sengketa</td>
            <td width="2%">:</td>
            <td>Setiap perselisihan yang timbul antara PARA PIHAK akan diselesaikan dengan musyawarah bersama, 
                ketika perselisihan tidak diselesaikan maka PARA PIHAK setuju untuk memilih kantor registrasi Pengadilan Negeri Jakarta Selatan.</td>
        </tr>
    </table>
    <p class="section">
        <strong>Ketentuan-ketentuan dan syarat-syarat lainnya yang disepakati PARA PIHAK:</strong>
    </p>
    
    <div class="section">
        <ol>
            <li><b>PIHAK PERTAMA</b> wajib menjamin Perusahaannya telah memiliki legalitas untuk mengangkut BBM & berlayar, menjamin legalitas kapal, memastikan bahwa kapalnya yang digunakan telah laik laut atau laik beroperasional, 
            mentaati Peraturan Perundang-undangan yang berlaku, menjamin keamanan dan keselamatan kerja, menjamin barang yang diangkut sampai tujuan dengan tepat waktu, tepat jumlah, bertanggung jawab penuh & menjamin atas kualitas dan 
            kuantitas cargo yang diangkut dalam kondisi baik (tidak terkontaminasi dan dalam spesifikasi standar mutu), serta bertanggung jawab terhadap pencemaran lingkungan, tumpahan produk dan efek terhadap insiden tersebut.</li>
            <li><b>PIHAK KEDUA</b> dengan ini menjamin bahwa bahan bakar yang diangkut oleh Kapal <b>PIHAK PERTAMA</b> adalah bahan bakar yang legal.</li>
            <li><b>PIHAK KEDUA</b> berhak untuk mendapatkan informasi dokumen kapal dari <b>PIHAK PERTAMA</b>, antara lain; 3 (tiga) kargo muat terakhir, Ship Particular, Tabel ukur tera tangki kapal, Vessel Experience Factor, General Arrangement (GA), hydraustatic table, data kapasitas tangki cargo dan non cargo, dan sebagainya yang berkaitan dengan dokumen kapal.</li>
            <li><b>PIHAK PERTAMA</b> memastikan tangki kapal dalam keadaan bersih dan kering (clean & dry), serta siap untuk menerima cargo muatan produk Pemilik Cargo yaitu Minyak Solar/ HSD, Apabila muatan yang diangkut 3 (tiga) kargo terakhir bukan Minyak Solar maka <b>PIHAK PERTAMA</b> diwajibkan melakukan pembersihan /cleaning pada tangki penyimpanan di kapal dan melampirkan dokumen hasil inspeksi kebersihan tangki “cleanliness certificate” yang diterbitkan oleh surveyor/ badan independen yang berwenang.</li>
            <li>Apabila oleh karena sesuatu dan lain hal atau menyangkut nautis sehingga kapal yang akan mengangkut mengalami keterlambatan atau hambatan, maka <b>PIHAK PERTAMA</b> diwajibkan untuk menggantikan dengan Kapal lainnya yang sama ukurannya tanpa merubah dari isi dan bunyi perjanjian ini dan harus memenuhi laycan atau tanggal muat yang telah disepakati.</li>
            <li><b>PIHAK PERTAMA</b> bertanggung jawab penuh terhadap kuantitas dan kualitas BBM dari Depot pengisian sampai dengan lokasi tujuan, baik itu terjadinya penyusutan dan kehilangan produk, kontaminasi produk, tumpahan produk dan sebagainya.</li>
            <li>Asuransi muatan (marine cargo insurance) adalah menjadi beban dan tanggung jawab <b>PIHAK KEDUA</b>. Asuransi Kapal ditanggung <b>PIHAK PERTAMA</b> termasuk asuransi hull & machinery, Protection & Indemnity (P&I), tanggung jawab hukum kepada pihak ketiga (Liability Insurance), namun tidak terbatas pada risiko tumpahan minyak dan pencemaran lingkungan yang diperlukan untuk mematuhi Peraturan yang berlaku.</li>
            <li>Sebelum melakukan pemuatan dan pembongkaran, PARA PIHAK dan / atau surveyor harus melakukan pemeriksaan kualitas, pengukuran volume bahan bakar, dan harus memastikan bahwa tutup segel dan saluran pipa masih dalam kondisi baik dan tidak rusak. Hasilnya harus dicatat dalam Catatan Pengiriman dan Bill of Lading yang ditandatangani oleh PARA PIHAK dan / atau Surveyor.</li>
            <li>Marine surveyor untuk melakukan supervisi kegiatan pemuatan dan pembongkaran di lokasi tujuan menjadi beban tanggung jawab <b>PIHAK KEDUA</b>.</li>
            <li><b>PIHAK PERTAMA</b> berkewajiban menginformasikan posisi kapal setiap hari nya secara periodik kepada <b>PIHAK KEDUA</b>.</li>
            <li>Crew kapal <b>PIHAK PERTAMA</b> baik itu Nahkoda, chief officer, Anak Buah Kapal (ABK), dan sebagainya, wajib memenuhi standar keselamatan kerja dan bekerja memenuhi Standar Operasional Prosedur (SOP) baik bekerja saat di Pelabuhan/lokasi muat, pengiriman dan di Pelabuhan Bongkar atau lokasi tujuan, serta menerapkan tanggap darurat apabila terjadi kecelakaan kerja.</li>
            <li>Penyusutan produk yaitu terjadi dikarenakan faktor temperatur atau sifat kimia produk. Apabila terjadi ketidaksesuaian segel dengan dokumen, kehilangan produk yang disebabkan kebocoran kapal, tindak pencurian /kriminal, terjadinya kontaminasi produk dan terjadinya penyusutan produk dengan volume lebih dari <?php echo $res[0]['loss_tolerance']; ?> dalam liter ’15 (GSV) pada titik serah terima R2 yaitu selisih volume antara Ship Figure After Loading vs Ship Figure Before Discharge, serta sebab lainnya, maka <b>PIHAK PERTAMA</b> bertanggung jawab penuh untuk mengganti, dan maka atas hal tersebut <b>PIHAK PERTAMA</b> bertanggung jawab dan mengganti kerugian dengan perhitungan; Volume dikali dengan harga BBM yang diasuransikan oleh <b>PIHAK KEDUA</b>.</li>
            <li>Apabila terjadi permasalahan di Pelabuhan/ lokasi muat dan bongkar, baik itu masalah kualitas (kontaminasi, dsb), kuantitas, baik itu penyusutan lebih dari 0.3% liter GSV, indikasi kecurangan dan permasalahan lainnya, <b>PIHAK PERTAMA</b> berkewajiban menginformasikan ke <b>PIHAK KEDUA</b> dan PARA PIHAK berkewajiban melakukan investigasi atas terjadinya insiden tersebut. Selama proses investigasi, kapal tidak diperbolehkan meninggalkan lokasi tanpa seizin <b>PIHAK KEDUA</b> dan tidak terikat oleh waktu, 
                serta atas hal tersebut tidak ada biaya tambahan atau biaya demurrage yang dapat di klaim ke <b>PIHAK KEDUA</b> . Terkait apabila adanya cargo yang tersisa di tangki cargo kapal atau Remaining On Board (ROB), <b>PIHAK KEDUA</b> akan melakukan Klaim ke <b>PIHAK PERTAMA</b> sesuai dengan ketentuan butir 12 (dua belas) pada perjanjian ini.</li>
            <li>Apabila selama Proses muat dan bongkar, kapal tidak dapat melakukan operasional dikarenakan perizinan kapal, keagenan kapal, baik dokumen dan sebagainya dan kapal mengalami kerusakan dan/atau yang disebabkan oleh teknis kapal, seperti kinerja pompa menurun saat bongkar, terindikasi kebocoran kapal dan sebagainya, serta tidak beroperasional dengan baik/layak, 
                maka <b>PIHAK PERTAMA</b> berkewajiban melakukan perbaikan dengan usaha yang maksimal dan atas hal tersebut maka waktu muat dan bongkar tidak terhitung sampai kapal dapat beroperasi dengan baik dan layak operasi, dan apabila kerusakan kapal tidak dapat diperbaiki dengan waktu maksimum 3x24 Jam dan mengakibatkan kapal tidak dapat beroperasi kembali dengan baik dan layak maka <b>PIHAK PERTAMA</b> berkewajiban mencari solusi alternatif yaitu salah satunya mencari kapal Pengganti dengan ukuran dan spesifikasi yang sama dengan kapal sebelumnya dan tanpa merubah isi kesepakatan dan ketentuan umum dalam Perjanjian ini. Apabila atas permasalahan tersebut mengakibatkan <b>PIHAK KEDUA</b> mengalami kerugian, baik itu timbul selisih harga yang sebabkan faktor keterlambatan kapal dan biaya klaim atau penalty dari konsumen dan supplier, maka <b>PIHAK KEDUA</b> akan membebankan kerugian tersebut kepada <b>PIHAK PERTAMA</b>.</li>
            <li>Terjadinya kebocoran pada pipa dan kapal, baik itu lambung kapal, tangki cargo dan sebagainya, serta kerusakan pada kapal atau hal lainnya yang mengakibatkan proses muat/bongkar dihentikan oleh pihak terkait, dimana terindikasi dapat membahayakan kegiatan operasional, maka <b>PIHAK PERTAMA</b> bertanggung jawab memberikan kapal pengganti dan/atau solusi atas hal tersebut dan 
                <b>PIHAK KEDUA</b> dibebaskan atas lamanya waktu muat dan bongkar dan biaya-biaya tambahan yang terjadi, serta <b>PIHAK PERTAMA</b> diwajibkan membuat laporan secara periodik setiap hari nya atas analisa masalah dan progress perbaikan, dan disampaikan ke <b>PIHAK KEDUA</b>.</li>
            <li>Apabila terjadi permasalahan terkait dengan pengiriman cargo baik itu kendala teknis kapal, kapal tidak dapat melewati jembatan, dokumen perizinan, permasalahan koordinasi dan sebagainya, maka <b>PIHAK PERTAMA</b> bertanggung jawab untuk menyelesaikan permasalahaan tersebut dan menjamin pengiriman cargo datang tepat waktu, 
                dan terkait dengan adanya timbul biaya atas hal tersebut dan adanya kerugian dari <b>PIHAK KEDUA</b>, maka menjadi beban <b>PIHAK PERTAMA</b>.</li>
            <li>Atas pengiriman muatan pada Perjanjian SPAL ini tidak terselesaikan oleh <b>PIHAK PERTAMA</b>, maka <b>PIHAK KEDUA</b> dibebaskan atau tidak dibebankan biaya jasa pengangkutan oleh <b>PIHAK PERTAMA</b>, serta atas timbulnya kerugian <b>PIHAK KEDUA</b> menjadi tanggung jawab sepenuhnya <b>PIHAK PERTAMA</b>.</li>
            <li>Keagenan kapal, biaya koordinasi dan biaya operasional lainya selama di Pelabuhan/lokasi muat, berlayar/pengiriman dan di pelabuhan/lokasi bongkar merupakan beban tanggung jawab sepenuhnya oleh <b>PIHAK PERTAMA</b>. 
                Dalam hal biaya angkutan/ Ongkos Angkut Transport (OAT) yang dibayarkan <b>PIHAK KEDUA</b> sesuai dengan kesepakatan dalam perjanjian ini yaitu sudah termasuk semua biaya-biaya yang timbul dalam pengiriman atau dengan kata lain <b>PIHAK KEDUA</b> tidak dibebankan lagi biaya diluar biaya Angkutan dan semua biaya menjadi tanggung jawab sepenuhnya <b>PIHAK PERTAMA</b>.</li>
            <li>Atas kelebihan waktu muat dan bongkar yaitu total 3 hari atau sama dengan 72 Jam (dihitung sejak kapal sandar dan berakhir sejak hose dilepas), dan kelebihan waktu tunggu kapal untuk sandar, baik di Pelabuhan muat dan di pelabuhan bongkar yaitu masing-masing 3 hari (72 jam), 
                maka atas kelebihan waktu tersebut maka <b>PIHAK KEDUA</b> akan dibebankan biaya demurrage.</li>
            <li>Alat ukur atau peralatan yang digunakan dalam kegiatan baik loading, discharge, terminal dan kapal melakukan pengukuran kuantitas dan pengecekkan kualitas harus memiliki sertifikat kalibrasi yang masih aktif yang diterbitkan oleh lembaga yang berwenang sesuai dengan peraturan yang berlaku,</li>
            <li><b>PIHAK PERTAMA</b> bertanggung jawab penuh terhadap kerusakan pada kapal baik yang disebabkan saat di Pelabuhan/lokasi muat, berlayar dan di Pelabuhan/lokasi bongkar.</li>
            <li><b>SALAH SATU PIHAK</b> tidak dapat mengakhiri perjanjian ini secara sepihak selama waktu yang telah disepakati.</li>
            <li><i>Force Majeur</i> dalam perjanjian ini adalah : badai, pasang surut, gempa bumi, sengatan petir, blokade, huru-hara, pernyataan darurat dari pemerintah serta hal lain yang bersifat di luar kemampuan alat dan manusia (<i>Act of God</i>) dimana dapat dibuktikan oleh dokumen dari otoritas terkait, tetapi tidak termasuk pemogokan tenaga kerja atau crew kapal yang disebabkan kesalahan <b>PIHAK PERTAMA</b>.</li>
            <li>Freight penuh harus dibayar dalam waktu yang ditetapkan sesuai charter party (c/p). "jika" terjadi klaim atas kehilangan kargo atau kontaminasi kargo, dll., yang disebabkan kesalahan <b>PIHAK PERTAMA</b>, <b>PIHAK KEDUA</b> harus mengirimkan invoice penuh ke kantor <b>PIHAK PERTAMA</b> disertai bukti klaim dengan dokumen pendukung yang valid. 
                Namun, jika kejadian tersebut disebabkan oleh kesalahan atau kelalaian pihak ketiga, seperti terminal, loading master, pengada, dll., yang tidak dinyatakan dalam charter party (c/p) ini, <b>PIHAK KEDUA</b> tidak memiliki hak untuk memotong freight untuk menutup kerugian tersebut atau menahan pembayaran freight. Jika tidak melakukan hal tersebut, 
                <b>PIHAK PERTAMA</b> berhak untuk menahan kargo <b>PIHAK KEDUA</b> hingga pembayaran freight penuh selesai. Segala biaya tambahan yang mungkin timbul dari insiden ini akan menjadi tanggung jawab dan biaya penuh <b>PIHAK KEDUA</b>.</li>
        </ol>
    </div>

    <div class="footer">
        <p>Demikian, PERJANJIAN ini dibuat dalam 2 (dua) rangkap asli, masing-masing bermaterai cukup dan mempunyai kekuatan hukum yang sama yang ditandatangani oleh <b>PARA PIHAK</b> pada hari dan tanggal tersebut pada bagian awal PERJANJIAN.</p>
        <table  border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
            <tr>
                <td>"PIHAK PERTAMA"</td>
                <td>"PIHAK KEDUA"</td>
            </tr>
            <tr>
                <td><?php echo $res[0]['nema_suplier']; ?></td>
                <td>PT PRO ENERGI</td>
            </tr>
            <tr class="jarak-bawah">
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Vica Krisdianatha</td>
            </tr>
            <tr>
                <td></td>
                <td>Direktur Utama</td>
            </tr>
        </table>
    </div>
</div>