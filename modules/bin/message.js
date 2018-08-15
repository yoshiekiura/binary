_Bbc(function($) {
  $("#loading").ajaxStart(function () {
    $(this).show()
  }).ajaxStop(function () {
    $(this).hide()
  });
  init();
  $('#form_mail').submit(function(){
    $.ajax({
      type: 'POST',
      url: url('action/message'),
      data: $(this).serialize(),
      success: function (a) {
        if (a.success) {
          $('#form_mail').get(0).reset();
        };
        $('#mail_output').html(a.message);
      }
    });
    return false;
  });
});
function init()
{
  $('.well.well-sm h3').click(function(){
    var a = $(this).parent().attr('id');
    if(typeof a == 'undefined') return false;
    var b = $(this).next('div');
    if(b.length)
    {
      $(b).toggle();
    }else{
      $.get(url('action/open?msg='+a), function(data){
        if (data.success) {
          $('#'+a).append(data.message);
          $('#'+a+' .reply').on('click focus', function(){
            showReply(this, true);
            return false;
          });
          $('#'+a+' i').click(function(){
            $(this).next('div').toggle();
          });
        }else{
          alert('Maaf, Message tidak bisa dibuka');
        }
      });
    }
    return false;
  });
};
function url(a)
{
  return (_URL+'bin/'+a+'?is_ajax='+(new Date()).getTime());
};
function showReply(a, b)
{
  if(b == true)
  {
    $(a).hide();
    var c = $(a).parent();
    $(c).append($('#mail_form').html());
    $(c).find('textarea').focus();
    $(c).find('textarea').blur(function(){
      if($(this).val() == '') {
        showReply(a, false);
      }
    });
  }else{
    $(a).show();
    $(a).parent().find('form').remove();
  }
};
function reply(a)
{
  var b = $(a).closest('.parent').attr('id');
  var c = $(a).closest('.well-sm').attr('id');
  var d = $('#'+b+' textarea').val();
  var e = (new RegExp(/([0-9]+)_([0-9]+)$/g)).exec(b);
  var f = (new RegExp(/([0-9]+)$/g)).exec(c);
  var g = f[1] ? f[1] : e[2];
  $.post(url('action/compose'), {'main_id':e[1],'par_id':g,'detail':d}, function(data){
    if (data.success) {
      var h = $(a).closest('.well-sm');
      var i = $(a).closest('.parent').find('.child');
      var j = new RegExp(/([0-9\.\,]+)/g);
      var k = j.exec($(i).html());
      if (k) {
        $(i).html($(i).html().replace(j, plusone(k[1])));
      };
      $(h).find('form').remove();
      $(h).append(data.message);
    }else{
      alert(data.message);
    }
  });
  return false;
};
function plusone(a)
{
  var b = (parseInt(a.replace(new RegExp(/[^0-9]/g), ""))+1).toString();
  var c = '';
  var d = 0;
  var e = b.length;
  for(var i=0;i<e;i++)
  {
    c+= b[i];
    d++;
    if(((e-d)%3)==0 && e != d) {
      c+=',';
    }
  }
  return c;
};
