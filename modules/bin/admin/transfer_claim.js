_Bbc(function($){
	$(document).ajaxStart(function(){
		$("#loading").show();
	}).ajaxStop(function(){
		$("#loading").hide();
	});
	$("#add_bin_id").on("change", function(){
		var a = $(this).val();
		if (a!="") {
			$.ajax({
			  url: document.location.href+"&id="+a+"&is_ajax=1",
			  dataType: "json",
			  success: function(a){
			  	var b = '';
			  	var c = '';
			  	var d = '';
			  	if (a.ok) {
			  		b = a.result.bonus;
			  		c = a.result.reward;
			  		d = a.result.amount;
			  	}else{
			  		alert(a.msg);
			  	}
			  	$("input[name='add_bonus']").val(b);
			  	$("input[name='add_reward']").val(c);
			  	$("input[name='add_amount']").val(d);
			  }
			});
		}
	}).trigger("change");
	$(".fa-sign-in").each(function(){
		var a = $(this).parent();
		a.unbind("click");
		a.removeAttr("rel").attr("target", "_blank");
	});
});