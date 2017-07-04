function tablewithbutton(idtabel,link,parameter,textbutton,idmodal,role){
  var table = $(idtabel).dataTable({
      "sPaginationType": "full_numbers",
  //            "bJQueryUI": true,
      "iDisplayLength": 30,
      "bDestroy": true,
      "bFilter": true,
      "bLengthChange": false,
      "responsive": true,
      "aaSorting": [],
      "bAutoWidth": true,
      "bSortable": false,
      "bSortClasses": true,
      "sAjaxSource": link,
      "fnServerParams": function (aoData) {
                 aoData.push(parameter);
             },
      dom: 'Bfrtip',
      buttons: [
          {
              text: textbutton,
              action: function (e, dt, node, config) {
                      $(idmodal).modal('show');


              }
          }
      ]
  });
}
function table(idtabel,link,parameter){
  var table = $(idtabel).dataTable({
      "sPaginationType": "full_numbers",
  //            "bJQueryUI": true,
      "iDisplayLength": 30,
      "bDestroy": true,
      "bFilter": true,
      "bLengthChange": false,
      "responsive": true,
      "aaSorting": [],
      "bAutoWidth": true,
      "bSortable": false,
      "bSortClasses": true,
      "sAjaxSource": link,
      "fnServerParams": function (aoData) {
                 aoData.push(parameter);
             }

  });
}
function closemodal(idform,idmodal){
  $(idmodal).modal('hide');
  $(idform).resetForm();
}
