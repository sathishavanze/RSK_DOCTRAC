$('.calculator').hide();

$('.calculatorIcon').click(function()
{
	$('.calculator').toggle();
});


function parseNumber(string) {
  if (string === undefined || string === '') {
    return '';
  }

  if (!isNaN(string)) {
    return parseFloat(string);
  }
};


//round up function

function calculate_roundup(number, digits) {
  number = parseNumber(number);
  digits = parseNumber(digits);
  var sign = (number > 0) ? 1 : -1;
  return sign * (Math.ceil(Math.abs(number) * Math.pow(10, digits))) / Math.pow(10, digits);
}
