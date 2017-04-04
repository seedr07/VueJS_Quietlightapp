module.exports = function (time) {

	var d = new Date(time*1000);
	var $a = $("<abbr>");
	$a.prop('title', d.toISOString());
	$a.addClass('timeago');
	$a.html(d.toISOString());

	return $a.get(0).outerHTML;
	
}
