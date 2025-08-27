<?php
    $_data = [];
    $sql = "
        select 
            a.*, 
            c.nama_terminal, 
            c.lokasi_terminal, 
            c.tanki_terminal, 
            d.nama_cabang, 
            if(a.is_loco = 0,e.arr_nomor_po,'LOCO') as arr_nomor_po 
        from 
            pro_po_ds a 
            join pro_master_terminal c on a.id_terminal = c.id_master 
            join pro_master_cabang d on a.id_wilayah = d.id_master 
            left join (
                select group_concat(a.nomor_po SEPARATOR '#') as arr_nomor_po, a.id_ds
                from(
                    select 
                        distinct a.id_po, 
                        a.id_ds, 
                        b.nomor_ds, 
                        c.nomor_po 
                    from 
                        pro_po_ds_detail a 
                        join pro_po_ds b on a.id_ds = b.id_ds 
                        join pro_po c on a.id_po = c.id_po
                ) a
                group by 
                    a.id_ds
            ) e on a.id_ds = e.id_ds
        where 
            a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'
    ";
    $result = $con->getResult($sql);
    foreach ($result as $key => $value) {
        $_data[$key]['id'] = $key+1;  
        $title = '';
        $title .= 'Kode DS: '.$value['nomor_ds'];
        // $tmp_nom_po = explode("#", $value['arr_nomor_po']);
        // $arr_nom_po = "";
        // foreach($tmp_nom_po as $row){
        //     $arr_nom_po .= '<p style="margin-bottom:0px; font-size:9px; font-family:arial;">'.$row.'</p>';
        // }
        // $title .= '<br />Nomor PO: '.$arr_nom_po;
        // $title .= '<br />Cabang: '.$value['nama_cabang'];
        $_data[$key]['title'] = $title;    
        $_data[$key]['description'] = null;    
        $_data[$key]['url'] = BASE_URL_CLIENT.'/delivery-loading-detail.php?'.paramEncrypt('idr='.$value['id_ds']);   
        $_data[$key]['start'] = $value['tanggal_ds']; // '2018-10-18T10:00:00'; 
        $_data[$key]['end'] = null;   
        // $_data[$key]['allDay'] = false;   
    }
?>
<script>
  $(document).ready(function() {
    function getData() {
        let data = JSON.parse('<?php echo json_encode($_data); ?>')
        return data
    }

    $('#calendar').fullCalendar({
      // defaultDate: '2018-03-12',
      defaultView: 'week',
      // defaultView: 'agendaWeek',
      views: {
          week: {
              type: 'basic', // basicWeek
              duration: { 
                  weeks: 1,
                  days:1, 
                  // hours:23, 
                  // minutes:59 
              }
          }
      },
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: getData(),
      left:   'title',
      center: '',
      right:  'today prev,next'
    });

  });

</script>
<style>

  /* body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  } */

  #calendar {
    /*max-width: 900px;*/
    margin: 0 auto;
  }

</style>

<div id='calendar'></div>