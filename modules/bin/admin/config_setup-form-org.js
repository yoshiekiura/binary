_Bbc(function($){
  $.fn.blink = function(a, b) {
    var a = $.extend({ delay: 300 }, a);
    return $(this).each(function(c, d) {
      var c = setInterval(function() {
        if ($(d).css("visibility") === "visible") {
          $(d).css('visibility', 'hidden');
        } else {
          $(d).css('visibility', 'visible');
        }
      }, a.delay);
      $(d).data('handle', c);
      var b = $.extend({ delay: 2000 }, b);
      setInterval(function() {
      	$(d).unblink();
      }, b.delay);
    });
  };
  $.fn.unblink = function() {
    return $(this).each(function(a, b) {
      var c = $(b).data('handle');
      if (c) {
        clearInterval(c);
        $(b).data('handle', null);
        $(b).css('visibility', 'inherit');
      }
    });
  };
	var A = [];
	$(".toggle").each(function(){
		A.push($(this).attr("target"));
	}).on("click", function(){
		var a = $($(this).attr("target"));
		var b = $($(this).attr("target")+"2");
		if($(this).is(":checked")) {
			$("input.form-control", a).each(function(){
				if (!$(this).hasClass("nochange")) {
					$(this).attr("req", "number true");
				}
			});
			if ($(this).attr("name").match(/_ok$/)) {
				$(".level", a).html("");
			}else
			if ($(this).attr("name").match(/_gen$/)) {
				$(".btn.reset", a).trigger("click");
			}
			a.show("slow");
			b.hide("slow");
			b.removeAttr("req");
		}else{
			$("input.form-control", a).each(function(){
				if (!$(this).hasClass("nochange")) {
					$(this).removeAttr("req");
				}
			});
			$(".level", a).html("");
			a.hide("slow");
			b.show("slow");
			b.attr("req", "number true");
		}
	});
	$(A.join(", ")).hide();
	$("#flushwait").on("click", function(){
		if ($(this).is(":checked")) {
			$("#flushwait_time").show("slow");
			$("#flushwait_duration").show("slow");
		}else{
			$("#flushwait_time").hide("slow");
			$("#flushwait_duration").hide("slow");
		}
	}).trigger("click");
	$("#serial_add").on("click", function(e){
		e.preventDefault();
    serialAdd();
	});
	$("#serial_name, #serial_price, #serial_flushout").on("keydown keypress", function(e){
    var a = e.charCode || e.which;
    if (a == 13) {
    	e.preventDefault();
    	$("#serial_add").trigger("click");
      return false
    }
	});
	$(".btn.add").click(function(){
		var a = $(this).parent();
		var b = $("#tpl_input").html();
		var c = $(".form-inline", a).length + 1;
		b = b.replace(/\{level\}/ig, c).replace(/\{name\}/ig, a.prop("id").replace(/_gen$/, ""));
		$(".level", a).append(b);
	});
	$(".btn.reset").click(function(){
		var a = $(this).parent();
		$(".level", a).html("");
		$(".btn.add", a).trigger("click");
	});
	/* START BONUS PASANGAN */
	// activation multiple serial
	$("#serial_use").on("change", function(){
		if($(this).is(":checked")) {
			$(".serial_flushout_ok").show(); // show checkbox flush per serial
		}else{
			$(".serial_flushout_ok").hide();
		}
		$("#serial_option2").trigger("change"); // in case field price has been marked as error
		$(".serial_flushout").trigger("change"); // in case field flush per serial has been marked as error
	}).trigger("change");
	// activatetion bonus pair
	$("#bonus_pair_ok").on("change", function(e){
		var a = false;
		var b = "number false";
		if ($(this).is(":checked")) {
			if ($("#serial_flushout_ok").is(":checked")) {
				a = true;
				b = "number true";
				$("#flushout_total").hide("slow").attr("req", "number false");
			}else{
				$("#flushout_total").show("slow").attr("req", "number true");
			}
			$(".serial_flushout").each(function(a){
				if ($(this).prop("id")!="serial_flushout") {
					$(this).attr("req", b);
				}
			});
		}
		if (a) {
			$(".serial_flushout").show().focus();
		}else{
			$(".serial_flushout").hide();
		}
	}).trigger("change");
	// activation flush per serial
	$("#serial_flushout_ok").on("change", function(){
		// check bonus pair activation
		$("#bonus_pair_ok").trigger("change");
		if ($(this).is(":checked")) {
			// find all field flush for every serial
			$(".serial_flushout").each(function(a){
				if ($(this).prop("id")!="serial_flushout") {
					if ($(this).val()=="") {
						$(this).focus().blink();
						return false;
					}
				}else{
					if (a == 0) {
						$(this).focus();
					}
					return false;
				}
			});
		}else{
			$("#flushout_total").focus();
			$(".serial_flushout").trigger("change");
		}
	});
	/* END BONUS PASANGAN */
	function serialAdd() {
		var a = $("#serial_option .serial_type");
		var b = $("#serial_name").val();
		var c = $("#serial_price").val();
		var d = $("#serial_flushout").val();
		if (b=="") {
			alert("masukkan nama tipe serial terlebih dahulu!");
			$("#serial_name").focus();
		}else
		if (c=="") {
			alert("masukkan biaya aktifasi untuk tipe serial '"+b+"' terlebih dahulu");
			$("#serial_price").focus();
		}else
		if (/[^0-9]/.test(c)) {
			alert("harap masukkan angka saja untuk biaya registrasi");
			$("#serial_price").select();
		}else{
			var ok = false;
			if ($("#serial_flushout_ok").is(":checked")) {
				if (d=="") {
					alert("masukkan total flushout untuk tipe serial '"+b+"' terlebih dahulu");
					$("#serial_flushout").focus();
				}else
				if (/[^0-9]/.test(d)) {
					alert("harap masukkan angka saja untuk total flushout");
					$("#serial_flushout").select();
				}else{
					ok = true;
				}
			}else{
				ok = true;
			}
			if (ok) {
				window.cSerial++;
				$("#serial_name").val("").focus();
				$("#serial_price").val("");
				$("#serial_flushout").val("");
				var e = $("#tpl_serial").html();
				var f = window.cSerial;
				e = e.replace(/\{level\}/ig, f).replace(/\{name\}/ig, b).replace(/\{value\}/ig, c).replace(/\{flushout\}/ig, d);
				a.append(e);
				$("#bonus_pair_ok").trigger("change");
				$("#serial_name").focus();
				$("a", $("#serial_"+f)).on("click", function(f){
					f.preventDefault();
					if (confirm("apakah anda yakin untuk menghapus tipe serial ini?")) {
						$(this).closest(".form-group").remove();
					}
				})
			}
		}
	};
});
var cSerial = 0;