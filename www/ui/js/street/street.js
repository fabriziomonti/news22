//-------------------------------------------------------------------------
function check_email(form)
	{
	if (form.email.value == '')
		{
		alert("Indicare l'indirizzo email");
		return false;
		}
	var emailPattern = /^[\w-\.]{1,}\@([\da-zA-Z-]{2,}\.){1,}[\da-zA-Z-]{2,4}$/i;
	if (!emailPattern.test(form.email.value))
		{
		alert("Indirizzo email non corretto");
		return false;
		}

	return true;
	}
