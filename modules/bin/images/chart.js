_Bbc(function($){
  $.ajax({
    url: _URL+'modules/bin/images/highcharts.js',
    dataType: "script",
    success: function(){
      $.ajax({
        url: _URL+'modules/bin/images/exporting.js',
        dataType: "script",
        success: function(){
          $(document).ajaxStart(function(){
            $("#loading").show();
          }).ajaxStop(function(){
            $("#loading").hide();
          });
          $("#link_prev, #link_next").on("click", function(e){
            e.preventDefault();
            fetch($(this).attr("href"));
          });
          fetch(document.location.href);
        }
      });
    }
  });
  function fetch(a) {
    a += /\?/.test(a) ? '&' : '?';
    a += 'is_ajax=1';
    $.ajax({
      url: a,
      dataType: "json",
      success: function(a){
        if (a.ok) {
          $("#container").highcharts(a.result);
          if (a.result.color) {
            if (a.result.color.green) {
              for (var i = 0; i < a.result.color.green.length; i++) {
                $( "text:contains('"+a.result.color.green[i]+"')" ).css( "color", "green" ).css("fill", "green");
              }
            }
            if (a.result.color.red) {
              for (var i = 0; i < a.result.color.red.length; i++) {
                $( "text:contains('"+a.result.color.red[i]+"')" ).css( "color", "red" ).css("fill", "red");
              }
            }
          }
        }else{
          $("#container").html('<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign" title="warning sign"></span> '+a.msg+'</div>');
        }
        if (a.prev) {
          $("#link_prev").attr("href", a.prev)
          .parent().show();
        }else{
          $("#link_prev").attr("href", "")
          .parent().hide();
        }
        if (a.next) {
          $("#link_next").attr("href", a.next)
          .parent().show();
        }else{
          $("#link_next").attr("href", "")
          .parent().hide();
        }
        if (a.result.table) {
          var b = '<table class="table table-striped table-bordered table-hover">';
          if (a.result.table.coloms) {
            b += '<thead><tr>';
            for (var i = 0; i < a.result.table.coloms.length; i++) {
              b += '<th>'+a.result.table.coloms[i]+'</th>';
            }
            b += '</tr></thead>';
          }
          if (a.result.table.rows) {
            b += '<tbody>';
            for (var i = 0; i < a.result.table.rows.length; i++) {
              var c = a.result.table.rows[i];
              b += '<tr>';
              for (var j = 0; j < c.length; j++) {
                b += '<td>'+c[j]+'</td>';
              }
              b += '</tr>';
            }
            b += '</tbody>';
          }
          b += '</table>';
          $("#myreport .table-responsive").html(b);
        }else{
          $("#myreport .table-responsive").html("");
        }
      }
    });
  }
});
