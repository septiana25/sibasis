/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });
  $("#nim").keyup(function (e) {
    var nim = $(this).val();
    var prodi = "";
    var fakultas = "";
    if ($.trim(nim) == "") {
      $("#msg").html("");
      $("#msg").removeClass("valid-tooltip invalid-tooltip");
      $("#ajukan").attr("disabled", true);
      $("#nim").removeClass("is-invalid is-valid");
    } else {
      if (nim) {
        $("#msg").html("<div class='spinner-border spinner-border-sm' role='status'></div>");
        $.ajax({
          type: "GET",
          url: WEB_URL + "/getdatamahasiswa/" + nim,
          dataType: "JSON",
          success: function (res) {
            if (res > "0") {
              $("#msg").html("NIM valid!");
              $("#msg").removeClass("invalid-tooltip").addClass("valid-tooltip");
              $("#ajukan").attr("disabled", false);
              $("#nim").removeClass("is-invalid").addClass("is-valid");
              $.each(res, function (index, data) {
                $("#nama").val(data.nama_lengkap);
                $("#fakultas").val(data.fakultas);
                $("#prodi").val(data.program_studi);
                $("#alamat").val(data.alamat);
                $("#nohp").val(data.nomor_handphone);
                $("#email").val(data.email);
              });
            } else {
              $("#nama").val("");
              $("#fakultas").val("");
              $("#prodi").val("");
              $("#alamat").val("");
              $("#nohp").val("");
              $("#email").val("");
              $("#msg").html("NIM tidak valid!");
              $("#msg").removeClass("valid-tooltip").addClass("invalid-tooltip");
              $("#ajukan").attr("disabled", true);
              $("#nim").removeClass("is-valid").addClass("is-invalid");
            }
          },
        });
      }
    }
  });
});