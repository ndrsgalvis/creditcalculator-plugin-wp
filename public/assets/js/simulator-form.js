jQuery(document).ready(function($){

    let interestRate = 0;
    let monetaryAmount =  0;
    let months =  0;
	let creditName = "";
	let url = "";

    let x = getParameterByName('mount');
    let a = getParameterByName('typeCredit');
    let b = getParameterByName('months');
   
    
    $("#show-request").css('display', 'none')
    $("#credit-type").on('change',function(){
        interestRate = parseFloat(this.value)
		creditName = $("#credit-type option:selected").text()
    });

    if( x !='' && a != '' && b != ''){
        $("#show-request").css('display', 'block')
        $("#empty-request").css('display', 'none')
        interestRate = parseFloat($("#credit-type").val())
		creditName = $("#credit-type option:selected").text()
        monetaryAmount = $("#currency-field").val()
        months = $("#months").val();
        getCuota();
		$("#credit-type").prop('disabled', true);
		$("#currency-field").prop('disabled', true);
		$("#currency-field").css('color', '#a6a6a6');
		$("#months").prop('disabled', true);
		$("#months").css('color', '#a6a6a6');
		$(".credit-data").text('Nueva simulación');
		url = "https://pick.com.co/formulario-de-credito?months="+months+"&mount="+monetaryAmount+"&typeCredit="+creditName;
    }

    $(".credit-data").click(function(){
		let textCreditData = $(".credit-data").text()
		
		if(textCreditData ==='Nueva simulación'){
		 	$(".credit-data").text('Simular')   
			
			$("#credit-type").prop('disabled', false);
			$("#currency-field").prop('disabled', false);
			$("#months").prop('disabled', false);
			
			$("#currency-field").css('color', '#000');
			$("#months").css('color', '#000');
		}
		
        monetaryAmount = $("#currency-field").val();
        months = $("#months").val();
        if(monetaryAmount != "" && months != "" && interestRate != 0){
            tempAmount = monetaryAmount.split(',').join('')
            monetaryAmount = tempAmount.slice(1);
            getCuota();
			
			url = "https://pick.com.co/formulario-de-credito?months="+months+"&mount="+monetaryAmount+"&typeCredit="+creditName;
        } else alert('Complete el formulario')
    });

    $(".credit-data-simple").click(function(){
        typeCredit = interestRate;
        months = $("#months").val();
        mount = $("#currency-field").val();
        

        if(mount != "" && months != "" && typeCredit != 0){
            tempAmount = mount.split(",").join("")
            mount = tempAmount.slice(1);

            window.location.replace("https://pick.com.co/simulador-cuotas?months=" +months+"&mount="+mount+"&typeCredit="+typeCredit);
           } else alert("Complete el formulario")

    });
	
	$(".modalContinue").click(function(){
		window.location.replace(url)
	})

	
    function getCuota(){
        quota = -PMT(interestRate, months, monetaryAmount, 0, 0)
        quota = formatNumber(quota.toFixed(0))
        
        $("#modalQuotaRate").html("$ " + quota)
        $("#modalMonthQuota").html("$ " + quota)
        $("#modalRate").html((interestRate * 100) + "%")
        $("#modalMonth").html(months)
        
        $("#mount").html("$ " + quota)
        $("#show-request").css('display', 'block')
        $("#empty-request").css('display', 'none')
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    // Quota per month
    function PMT(rate, nper, pv, fv, type) {
        if (!fv) fv = 0;
        if (!type) type = 0;
        if (rate == 0) return -(pv + fv) / nper;

        var pvif = Math.pow(1 + rate, nper);
        var pmt = rate / (pvif - 1) * -(pv * pvif + fv);

        if (type == 1) {
            pmt /= (1 + rate);
        };
        return pmt;
    }
        
     // Input Currency format
     $("input[data-type='currency']").on({
        keyup: function() {
        formatCurrency($(this));
        },
        blur: function() { 
        formatCurrency($(this), "blur");
        }
    });
    
    function formatNumber(n) {
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        var input_val = input.val();
        
        if (input_val === "") { return; }
        
        var original_len = input_val.length;
        var caret_pos = input.prop("selectionStart");
            
        if (input_val.indexOf(".") >= 0) {

            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            left_side = formatNumber(left_side);
            right_side = formatNumber(right_side);
          
            right_side = right_side.substring(0, 2);
            input_val = "$" + left_side + "." + right_side;

        } else {
            input_val = formatNumber(input_val);
            input_val = "$" + input_val;
          
        }
        input.val(input_val);
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }
    

})