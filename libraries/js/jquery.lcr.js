$(document).ready(function () {
  var lat = $("#latitude").val();
  var long = $("#longitude").val();
  if (lat != "" && long != "") {
    previewmap(lat, long);
  }

  $(".hitung").number(true, 0, ".", ",");
  $("form#gform").validationEngine("attach");
  $("select#prov_lokasi").change(function () {
    $("select#kab_lokasi").val("").trigger("change").select2("close");
    $("select#kab_lokasi option").remove();
    if ($("#id_wil_oa").length) {
      $("select#id_wil_oa").val("").trigger("change").select2("close");
      $("select#id_wil_oa option").remove();
    }
    $.ajax({
      type: "POST",
      url: "./__get_kabupaten.php",
      dataType: "json",
      data: { q1: $("select#prov_lokasi").val() },
      cache: false,
      success: function (data) {
        if (data.items != "") {
          $("select#kab_lokasi").select2({
            data: data.items,
            placeholder: "Pilih salah satu",
            allowClear: true,
          });
          return false;
        }
      },
    });
  });

  $("select#kab_lokasi").change(function () {
    if ($("#id_wil_oa").length) {
      $("select#id_wil_oa").val("").trigger("change").select2("close");
      $("select#id_wil_oa option").remove();
      if ($("select#prov_lokasi").val() && $("select#kab_lokasi").val()) {
        $.ajax({
          type: "POST",
          url: "./__get_wilayah_oa.php",
          dataType: "json",
          data: {
            q1: $("select#prov_lokasi").val(),
            q2: $("select#kab_lokasi").val(),
          },
          cache: false,
          success: function (data) {
            if (data.items != "") {
              $("select#id_wil_oa").select2({
                data: data.items,
                placeholder: "Pilih salah satu",
                allowClear: true,
              });
              return false;
            }
          },
        });
      }
    }
  });

  $(".select-other").select2({
    placeholder: "- Pilihan -",
    allowClear: true,
    tags: true,
  });

  $("#lihat_map").click(function () {
    var lat = $("#latitude").val();
    var long = $("#longitude").val();
    if (lat != "" && long != "") {
      // Fungsi untuk mengambil data dari API
      async function getDataFromAPI(apiUrl) {
        try {
          // Melakukan permintaan HTTP GET
          const response = await fetch(apiUrl);

          // Mengecek apakah permintaan berhasil
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }

          // Mengonversi respons ke format JSON
          const data = await response.json();

          // Mengembalikan data JSON
          return data;
        } catch (error) {
          console.error("Terjadi kesalahan:", error);
          // Mengembalikan kesalahan atau melakukan penanganan kesalahan lainnya
          return null;
        }
      }

      // Contoh penggunaan
      const apiUrl =
        "https://nominatim.oslog.id/reverse.php?lat=" +
        lat +
        "&lon=" +
        long +
        "&zoom=18&format=json&addressdetails=1&limit=1";
      getDataFromAPI(apiUrl).then((data) => {
        if (data.data.way_id == 0 && data.data.wadm_id == 0) {
          Swal.fire({
            title: "Ooppss",
            text: "Latitude atau Longitude tidak valid",
            icon: "warning",
          });
          $("#btnSbmt").attr("disabled", true);
          $("#map_canvas").html("").removeAttr("style");
        } else {
          $("#loading_modal").modal();
          previewmap(lat, long);
          $("#loading_modal").modal("hide");
          $("#btnSbmt").removeAttr("disabled");
        }
      });
    }
  });

  function previewmap(lat, long) {
    if (lat == "" || long == "") {
      $("#map_canvas").html("").removeAttr("style");
      return false;
    } else {
      var latlng = new google.maps.LatLng(lat, long);
      var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };
      map = new google.maps.Map(
        document.getElementById("map_canvas"),
        myOptions
      );
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
      });
    }
  }

  $("#lihat_map2").click(function () {
    var link = $("#link_google_maps").val();
    var regex = new RegExp("@(.*),(.*),");
    var lon_lat_match = link.match(regex);
    var lat = lon_lat_match[1];
    var long = lon_lat_match[2];
    if (link != "") {
      $("#loading_modal").modal();
      previewmap(lat, long);
      $("#loading_modal").modal("hide");
    }
  });

  $("#gform").on("click", ".tbl-surveyor button.addRow", function () {
    $("form#gform").validationEngine("detach");
    var tabel = $(this).parents(".tbl-surveyor");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noSurveyor").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noSurveyor" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<input type="text" name="surveyor[]" id="surveyor' +
        newId +
        '" class="form-control validate[required] input-sm" />'
    );
    objTd3.html(
      '<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd3.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);
    tabel.find(".noSurveyor").each(function (i, v) {
      $(this).text(i + 1);
    });
    $("form#gform").validationEngine("attach");
  });
  $("#gform").on("click", ".tbl-surveyor a.hRow", function () {
    var tabel = $(this).parents(".tbl-surveyor");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noSurveyor").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-hasil button.addRow", function () {
    $("form#gform").validationEngine("detach");
    var tabel = $(this).parents(".tbl-hasil");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noHasil").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html('<span class="noHasil" data-row-count="' + newId + '"></span>');
    objTd2.html(
      '<input type="text" name="hasilsurv[]" id="hasilsurv' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd3.html(
      '<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd3.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);
    tabel.find(".noHasil").each(function (i, v) {
      $(this).text(i + 1);
    });
    $("form#gform").validationEngine("attach");
  });
  $("#gform").on("click", ".tbl-hasil a.hRow", function () {
    var tabel = $(this).parents(".tbl-hasil");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noHasil").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-produkvol button.addRow", function () {
    var tabel = $(this).parents(".tbl-produkvol");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noProdukvol").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noProdukvol" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<input type="text" name="produk[]" id="produk' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd3.html(
      '<input type="text" name="volbul[]" id="volbul' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd4.html(
      '<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd4.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);
    tabel.find(".noProdukvol").each(function (i, v) {
      $(this).text(i + 1);
    });
  });
  $("#gform").on("click", ".tbl-produkvol a.hRow", function () {
    var tabel = $(this).parents(".tbl-produkvol");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noProdukvol").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-picustomer button.addRow", function () {
    $("form#gform").validationEngine("detach");
    var tabel = $(this).parents(".tbl-picustomer");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noPicustomer").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noPicustomer" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<input type="text" name="namacus[]" id="namacus' +
        newId +
        '" class="form-control validate[required] input-sm" />'
    );
    objTd3.html(
      '<input type="text" name="posisicus[]" id="posisicus' +
        newId +
        '" class="form-control validate[required] input-sm" />'
    );
    objTd4.html(
      '<input type="text" name="telpcus[]" id="telpcus' +
        newId +
        '" class="form-control validate[required] input-sm" />'
    );
    objTd5.html(
      '<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd5.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);
    $("form#gform").validationEngine("attach");
    tabel.find(".noPicustomer").each(function (i, v) {
      $(this).text(i + 1);
    });
  });
  $("#gform").on("click", ".tbl-picustomer a.hRow", function () {
    var tabel = $(this).parents(".tbl-picustomer");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noPicustomer").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kompetitor button.addRow", function () {
    var tabel = $(this).parents(".tbl-kompetitor");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKompetitor").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noKompetitor" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<input type="text" name="kompetitor[]" id="kompetitor' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd3.html(
      '<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd3.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);
    tabel.find(".noKompetitor").each(function (i, v) {
      $(this).text(i + 1);
    });
  });
  $("#gform").on("click", ".tbl-kompetitor a.hRow", function () {
    var tabel = $(this).parents(".tbl-kompetitor");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKompetitor").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-tangki button.addRow", function () {
    var tabel = $(this).parents(".tbl-tangki");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noTangki").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd8 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noTangki" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="tangki[tipe][]" id="tangkiTipe_' +
        newId +
        '" class="form-control select-other"><option></option><option>Mobile</option><option>Inline</option><option>Underground</option><option>Drum</option><option>IBC</option></select>'
    );
    objTd3.html(
      '<input type="text" name="tangki[kapasitas][]" id="tangkiKapasitas_' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd4.html(
      '<input type="text" name="tangki[jumlah][]" id="tangkiJumlah_' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd5.html(
      '<select name="tangki[produk][]" id="tangkiProduk_' +
        newId +
        '" class="form-control select-other"><option></option><option>HSD</option><option>Bensin</option><option>Oli</option><option>Zat Kimia</option></select>'
    );
    objTd6.html(
      '<select name="tangki[inlet][]" id="tangkiInlet_' +
        newId +
        '" class="form-control select-other"><option></option><option>Camlock</option><option>Pipa</option><option>Manhole</option><option>Flange</option></select>'
    );
    objTd7.html(
      '<select name="tangki[ukuran][]" id="tangkiUkuran_' +
        newId +
        '" class="form-control select-other"><option></option><option>1 In</option><option>1.5 In</option><option>2 In</option><option>3 In</option></select>'
    );
    objTd8.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd8.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noTangki").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#tangkiTipe_" +
        newId +
        ", #tangkiProduk_" +
        newId +
        ", #tangkiInlet_" +
        newId +
        ", #tangkiUkuran_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-tangki a.hRow", function () {
    var tabel = $(this).parents(".tbl-tangki");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noTangki").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-support button.addRow", function () {
    var tabel = $(this).parents(".tbl-support");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noSupport").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd8 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noSupport" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="support[pompa][]" id="supportPompa_' +
        newId +
        '" class="form-control select-other"><option></option><option>Pelanggan</option><option>Transportir</option></select>'
    );
    objTd3.html(
      '<select name="support[aliran][]" id="supportAliran_' +
        newId +
        '" class="form-control select-other"><option></option><option>300 LPM</option><option>500 LPM</option><option>N/A</option></select>'
    );
    objTd4.html(
      '<select name="support[selang][]" id="supportSelang_' +
        newId +
        '" class="form-control select-other"><option></option><option>5 M</option><option>10 M</option><option>15 M</option><option>20 M</option></select>'
    );
    objTd5.html(
      '<select name="support[valve][]" id="supportValve_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ada</option><option>Tidak Ada</option></select>'
    );
    objTd6.html(
      '<select name="support[grounding][]" id="supportGrounding_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ada</option><option>Tidak</option></select>'
    );
    objTd7.html(
      '<select name="support[sinyal][]" id="supportSinyal_' +
        newId +
        '" class="form-control select-other"><option></option><option>Telkomsel</option><option>XL</option><option>Indosat</option><option>N/A</option></select>'
    );
    objTd8.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd8.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noSupport").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#supportPompa_" +
        newId +
        ", #supportAliran_" +
        newId +
        ", #supportSelang_" +
        newId +
        ", #supportValve_" +
        newId +
        ", #supportGrounding_" +
        newId +
        ", #supportSinyal_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-support a.hRow", function () {
    var tabel = $(this).parents(".tbl-support");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noSupport").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kuantitas1 button.addRow", function () {
    var tabel = $(this).parents(".tbl-kuantitas1");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKuantitas1").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noKuantitas1" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="kuantitas1[alat][]" id="kuantitas1Alat_' +
        newId +
        '" class="form-control select-other"><option></option><option>Jembatan Timbang</option><option>TUM</option><option>Flow Meter</option></select>'
    );
    objTd3.html(
      '<select name="kuantitas1[merk][]" id="kuantitas1Merk_' +
        newId +
        '" class="form-control select-other"><option></option><option>LC M10</option><option>Tokico</option></select>'
    );
    objTd4.html(
      '<select name="kuantitas1[tera][]" id="kuantitas1Tera_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ada</option><option>Tidak</option></select>'
    );
    objTd5.html(
      '<select name="kuantitas1[masa][]" id="kuantitas1Masa_' +
        newId +
        '" class="form-control select-other"><option></option><option>Berlaku</option><option>Kadaluarsa</option></select>'
    );
    objTd6.html(
      '<select name="kuantitas1[flowmeter][]" id="kuantitas1Flowmeter_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd7.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd7.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noKuantitas1").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#kuantitas1Alat_" +
        newId +
        ", #kuantitas1Merk_" +
        newId +
        ", #kuantitas1Tera_" +
        newId +
        ", #kuantitas1Masa_" +
        newId +
        ", #kuantitas1Flowmeter_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-kuantitas1 a.hRow", function () {
    var tabel = $(this).parents(".tbl-kuantitas1");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKuantitas1").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kualitas1 button.addRow", function () {
    var tabel = $(this).parents(".tbl-kualitas1");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKualitas1").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noKualitas1" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="kualitas1[spec][]" id="kualitas1Spec_' +
        newId +
        '" class="form-control select-other"><option></option><option>Migas</option></select>'
    );
    objTd3.html(
      '<select name="kualitas1[lab][]" id="kualitas1Lab_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd4.html(
      '<select name="kualitas1[coq][]" id="kualitas1Coq_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd5.html("&nbsp;");
    objTd6.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd6.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noKualitas1").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#kualitas1Spec_" +
        newId +
        ", #kualitas1Lab_" +
        newId +
        ", #kualitas1Coq_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-kualitas1 a.hRow", function () {
    var tabel = $(this).parents(".tbl-kualitas1");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKualitas1").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kapal button.addRow", function () {
    var tabel = $(this).parents(".tbl-kapal");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKapal").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd8 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html('<span class="noKapal" data-row-count="' + newId + '"></span>');
    objTd2.html(
      '<select name="kapal[tipe][]" id="kapalTipe_' +
        newId +
        '" class="form-control select-other"><option></option><option>Tugboat</option><option>SPOB</option><option>Tanker</option></select>'
    );
    objTd3.html(
      '<input type="text" name="kapal[kapasitas][]" id="kapalKapasitas_' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd4.html(
      '<input type="text" name="kapal[jumlah][]" id="kapalJumlah_' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd5.html(
      '<select name="kapal[inlet][]" id="kapalInlet_' +
        newId +
        '" class="form-control select-other"><option></option><option>Manhole</option><option>Pipa</option><option>Camlock</option><option>Flange</option></select>'
    );
    objTd6.html(
      '<select name="kapal[ukuran][]" id="kapalUkuran_' +
        newId +
        '" class="form-control select-other"><option></option><option>1 In</option><option>1.5 In</option><option>2 In</option><option>3 In</option></select>'
    );
    objTd7.html(
      '<select name="kapal[metode][]" id="kapalMetode_' +
        newId +
        '" class="form-control select-other"><option></option><option>STS</option><option>Truck to Ship</option></select>'
    );
    objTd8.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd8.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noKapal").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#kapalTipe_" +
        newId +
        ", #kapalMetode_" +
        newId +
        ", #kapalInlet_" +
        newId +
        ", #kapalUkuran_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-kapal a.hRow", function () {
    var tabel = $(this).parents(".tbl-kapal");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKapal").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-jetty button.addRow", function () {
    var tabel = $(this).parents(".tbl-jetty");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noJetty").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd8 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html('<span class="noJetty" data-row-count="' + newId + '"></span>');
    objTd2.html(
      '<select name="jetty[loa][]" id="jettyLoa_' +
        newId +
        '" class="form-control select-other"><option></option><option>50 M</option><option>100 M</option><option>N/A</option></select>'
    );
    objTd3.html(
      '<select name="jetty[pbl][]" id="jettyPbl_' +
        newId +
        '" class="form-control select-other"><option></option><option>20 M</option><option>30 M</option><option>40 M</option><option>N/A</option></select>'
    );
    objTd4.html(
      '<input type="text" name="jetty[lws][]" id="jettyLws_' +
        newId +
        '" class="form-control input-sm" />'
    );
    objTd5.html(
      '<select name="jetty[sandar][]" id="jettySandar_' +
        newId +
        '" class="form-control select-other"><option></option><option>Max 5.000</option><option>Max 10.000</option></select>'
    );
    objTd6.html(
      '<select name="jetty[izin][]" id="jettyIzin_' +
        newId +
        '" class="form-control select-other"><option></option><option>Telsus</option><option>TUKS</option><option>N/A</option></select>'
    );
    objTd7.html(
      '<select name="jetty[syarat][]" id="jettySyarat_' +
        newId +
        '" class="form-control select-other"><option></option><option>Q88</option><option>Depot Approval</option><option>PSA</option></select>'
    );
    objTd8.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd8.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noJetty").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#jettyLoa_" +
        newId +
        ", #jettyPbl_" +
        newId +
        ", #jettySandar_" +
        newId +
        ", #jettyIzin_" +
        newId +
        ", #jettySyarat_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-jetty a.hRow", function () {
    var tabel = $(this).parents(".tbl-jetty");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noJetty").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kuantitas2 button.addRow", function () {
    var tabel = $(this).parents(".tbl-kuantitas2");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKuantitas2").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd7 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noKuantitas2" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="kuantitas2[alat][]" id="kuantitas2Alat_' +
        newId +
        '" class="form-control select-other"><option></option><option>Tanki Darat</option><option>Tanki Kapal</option><option>Flow Meter</option></select>'
    );
    objTd3.html(
      '<select name="kuantitas2[merk][]" id="kuantitas2Merk_' +
        newId +
        '" class="form-control select-other"><option></option><option>LC M10</option><option>Tokico</option></select>'
    );
    objTd4.html(
      '<select name="kuantitas2[tera][]" id="kuantitas2Tera_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ada</option><option>Tidak</option></select>'
    );
    objTd5.html(
      '<select name="kuantitas2[masa][]" id="kuantitas2Masa_' +
        newId +
        '" class="form-control select-other"><option></option><option>Berlaku</option><option>Kadaluarsa</option></select>'
    );
    objTd6.html(
      '<select name="kuantitas2[flowmeter][]" id="kuantitas2Flowmeter_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd7.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd7.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noKuantitas2").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#kuantitas2Alat_" +
        newId +
        ", #kuantitas2Merk_" +
        newId +
        ", #kuantitas2Tera_" +
        newId +
        ", #kuantitas2Masa_" +
        newId +
        ", #kuantitas2Flowmeter_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-kuantitas2 a.hRow", function () {
    var tabel = $(this).parents(".tbl-kuantitas2");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKuantitas2").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });

  $("#gform").on("click", ".tbl-kualitas2 button.addRow", function () {
    var tabel = $(this).parents(".tbl-kualitas2");
    var rwTbl = tabel.find("tbody > tr:last");
    var rwNom = parseInt(rwTbl.find("span.noKualitas2").data("rowCount"));
    var newId = parseInt(rwNom + 1);

    var objTr = $("<tr>");
    var objTd1 = $("<td>", { class: "text-center" }).appendTo(objTr);
    var objTd2 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd3 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd4 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd5 = $("<td>", { class: "text-left" }).appendTo(objTr);
    var objTd6 = $("<td>", { class: "text-center" }).appendTo(objTr);
    objTd1.html(
      '<span class="noKualitas2" data-row-count="' + newId + '"></span>'
    );
    objTd2.html(
      '<select name="kualitas2[spec][]" id="kualitas2Spec_' +
        newId +
        '" class="form-control select-other"><option></option><option>Migas</option></select>'
    );
    objTd3.html(
      '<select name="kualitas2[lab][]" id="kualitas2Lab_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd4.html(
      '<select name="kualitas2[coq][]" id="kualitas2Coq_' +
        newId +
        '" class="form-control select-other"><option></option><option>Ya</option><option>Tidak</option></select>'
    );
    objTd5.html("&nbsp;");
    objTd6.html(
      '<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> '
    );
    objTd6.append(
      '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'
    );
    rwTbl.after(objTr);

    tabel.find(".noKualitas2").each(function (i, v) {
      $(this).text(i + 1);
    });
    $(
      "#kualitas2Spec_" +
        newId +
        ", #kualitas2Lab_" +
        newId +
        ", #kualitas2Coq_" +
        newId
    ).select2({
      placeholder: "- Pilihan -",
      allowClear: true,
      tags: true,
    });
  });
  $("#gform").on("click", ".tbl-kualitas2 a.hRow", function () {
    var tabel = $(this).parents(".tbl-kualitas2");
    var jTbl = tabel.find("tr").length;
    if (jTbl > 2) {
      var cRow = $(this).closest("tr");
      cRow.remove();
      tabel.find(".noKualitas2").each(function (i, v) {
        $(this).text(i + 1);
      });
    }
  });
});
