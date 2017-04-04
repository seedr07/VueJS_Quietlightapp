module.exports = function (user) {
	var name = '';

	if(user.first_name)
		return user.first_name + ((user.last_name) ? " " + user.last_name : "");

	if(user.email)
		return user.email;

	if(user.id)
		return "#"+user.id;

	return "Someone";
}