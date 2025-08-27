var todaysDate = new Date();
$(function () {
  $(".datepicker").datepicker({
    /*showOn: "button",
		buttonImage: location.protocol+"//"+location.hostname+"/images/calendar.gif",
		buttonImageOnly: true,*/
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "c-80:c+10",
    dayNamesMin: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    monthNamesShort: [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ],
  });

  $(".datepickermax").datepicker({
    /*showOn: "button",
		buttonImage: location.protocol+"//"+location.hostname+"/images/calendar.gif",
		buttonImageOnly: true,*/
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "c-80:c+10",
    dayNamesMin: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    monthNamesShort: [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ],
    maxDate: todaysDate,
  });

  $(".timepicker").timepicker({
    timeFormat: "HH:mm",
    controlType: "select",
    oneLine: true,
  });
});
