module.exports = function (value) {
	if(!value) return;
	
	// Convert the value to a floating point number in case it arrives as a string.
	value2 = value;
	if(value.replace) value2 = value2.replace(/[\$,]/g, "");
    var numeric = parseFloat(value2);

    //If we couldn't parse it as a number, return original value.
    if(isNaN(numeric)) {
    	return value;	
    } 
    
    // Specify the local currency.
    return numeric.toLocaleString('USD', { style: 'currency', currency: "USD", minimumFractionDigits: 2, maximumFractionDigits: 2 });
};