$('.menu__each--dropdown').hide();
$('.menu__each--toggle').on('click', function() {
  $(this).find('.fas').toggleClass('fa-angle-down fa-angle-up');
  $(this).next().slideToggle(500).siblings('.menu__each--dropdown').slideUp(500);
});

function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
  return true;
}



$('#alert').click(function(e) {
  $(this).find('.notifications').toggleClass('inactive active');
  $(this).next().find('.notifications').addClass('inactive').removeClass('active');
  e.stopPropagation();
})

$('#welcome').click(function(e) {
  $(this).find('.notifications').toggleClass('inactive active');
  $(this).prev().find('.notifications').addClass('inactive').removeClass('active');
  e.stopPropagation();
})

$(document).click(function() {
  $(".notifications").addClass('inactive').removeClass('active');
});


$("#sidebar__toggle").click(function(e) {
  var sidebarleft = $("#sidebar__slider").css('left');
  var sidebarwidth = $("#sidebar__slider").css('width');
  var width = $(window).width();
  var height = $(window).height();

  if ((width >= 720)) {
    if (sidebarleft == "0px") {
      $("#sidebar__slider").animate({
        left: -1 * parseInt(sidebarwidth)
      });
      $(".main-content").animate({
        marginLeft: 0
      });
    } else {
      $("#sidebar__slider").animate({
        left: 0
      });
      $(".main-content").animate({
        marginLeft: 20 + '%'
      })
    }
  } else {
    if (sidebarleft == "0px") {
      $("#sidebar__slider").animate({
        left: -1 * parseInt(sidebarwidth)
      });
      $(".main-content").animate({
        marginLeft: 0
      });
    } else {
      $("#sidebar__slider").animate({
        left: 0
      });
      $(".main-content").animate({
        marginLeft: 0 + '%'
      })
    }
  }
});


$("#checkAll").click(function() {
  $('input:checkbox').not(this).prop('checked', this.checked);
});

$("#btn--check").click(function() {
  console.log('test');
  $(this).find('#checkAll').prop('checked');
});

//chart


var lineChartData = {
  labels: ['21/07/2018', '22/07/2018', '23/07/2018', '24/07/2018', '25/07/2018', '26/07/2018', '27/07/2018'],
  datasets: [{
    label: 'Bonus Pasangan',
    borderColor: [
      'rgba(41,182,246,1)'
    ],
    backgroundColor: [
      'rgba(214,25,24,0)'
    ],
    fill: false,
    data: [
      10,
      2,
      2,
      15,
      15,
      5,
      5
    ],
    yAxisID: 'y-axis-1',
  }, {
    label: 'Bonus Sponsor',
    borderColor: [
      'rgba(244,67,54,1)'
    ],
    backgroundColor: [
      'rgba(214,25,24,0)'
    ],
    fill: false,
    data: [
      0,
      10,
      10,
      50,
      0,
      20,
      10
    ],
    yAxisID: 'y-axis-2'
  }]
};

window.onload = function() {
  var ctx = document.getElementById('myChart');
  ctx.height = 300;
  window.myLine = Chart.Line(ctx, {
    data: lineChartData,
    options: {
      maintainAspectRatio: false,
      responsive: true,
      hoverMode: 'index',
      stacked: true,
      title: {
        display: true,
        text: 'Status Komisi'
      },
      scales: {
        yAxes: [{
          type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
          display: true,
          position: 'left',
          id: 'y-axis-1',
        }, {
          type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
          display: false,
          id: 'y-axis-2',

          // grid line settings
          gridLines: {
            drawOnChartArea: false, // only want the grid lines for one axis to show up
          },
        }],
      }
    }
  });
};

// datepicker
$(function() {

  var start = moment().subtract(7, 'days');
  var end = moment();

  function cb(start, end) {
    $('input[name="historikomisi"]').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#to').val(start.format('MMMM D, YYYY'));
    $('#from').val(end.format('MMMM D, YYYY'));
  }

  $('input[name="historikomisi"]').daterangepicker({
    startDate: start,
    endDate: end,
    "opens": "left",
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }, cb);

  cb(start, end);

});

$('input[name="birthdate"]').daterangepicker({
  singleDatePicker: true,
  showDropdowns: true,
  locale: {
    format: 'DD/MM/YYYY'
  },
});