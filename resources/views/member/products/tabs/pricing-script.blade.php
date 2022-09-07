<script>
var types = ['merchandise', 'freight', 'crati', 'installLabor', 'designFee', 'time', 'total'];
var cols = ['cost', 'profit', 'price', 'salesTax', 'total'];
$("#pricing-table input").keyup(() => updatePricing());

const updatePricing = () => {
  types.forEach(type => {
      
      
    var profit_val = $(`#input-pricing-${type}-profit`).val();
    var key_val = "%";
    
    if(profit_val.indexOf(key_val) != -1){
        
        var percentage = profit_val.replace("%", "");
        var total_val = $(`#input-pricing-${type}-cost`).val();
        var new_val = (percentage / 100) * total_val;
        
        const price_val = (Number(total_val) + Number(new_val)).toFixed(2);
        $(`#input-pricing-${type}-price`).val(price_val);
        
    } else {
        
        const price_val = (Number($(`#input-pricing-${type}-cost`).val()) + Number($(`#input-pricing-${type}-profit`).val())).toFixed(2);
        $(`#input-pricing-${type}-price`).val(price_val);
        
    }
      
   
      
    const total = Number($(`#input-pricing-${type}-price`).val()) + Number($(`#input-pricing-${type}-salesTax`).val());
    $(`#input-pricing-${type}-total`).val(total);
    
  })
  
  cols.forEach(col => {
      
    //const total_col = Number($(`#input-pricing-merchandise-${col}`).val()) + Number($(`#input-pricing-freight-${col}`).val()) + Number($(`#input-pricing-crati-${col}`).val()) + Number($(`#input-pricing-installLabor-${col}`).val()) + Number($(`#input-pricing-designFee-${col}`).val()) + Number($(`#input-pricing-time-${col}`).val()) + Number($(`#input-pricing-total-${col}`).val());
    const total_col = (Number($(`#input-pricing-merchandise-${col}`).val().replace("%", "")) + Number($(`#input-pricing-freight-${col}`).val().replace("%", "")) + Number($(`#input-pricing-crati-${col}`).val().replace("%", "")) + Number($(`#input-pricing-installLabor-${col}`).val().replace("%", "")) +  Number($(`#input-pricing-designFee-${col}`).val().replace("%", "")) +  Number($(`#input-pricing-time-${col}`).val().replace("%", ""))).toFixed(2) ;
    $(`#input-pricing-total-${col}`).val(total_col);
    
  })
}

$("#default_markup").change(function(){
    var markup = Number($("#default_markup") .val());
    var cost = Number($("#cost_per_unit") .val());
    var msrp = 0;
    msrp = Number(cost+((markup/100)*cost)).toFixed(2);
    $("#msrp") .val(msrp);
    
    
  
});

$("#cost_per_unit").change(function(){
    
    var markup = Number($("#default_markup") .val());
    var cost = Number($("#cost_per_unit") .val());
    var msrp = 0;
    msrp = Number(cost+((markup/100)*cost)).toFixed(2);
    $("#msrp") .val(msrp);
  
});




</script>