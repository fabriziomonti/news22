//-------------------------------------------------------------------------
function validaForm(form)
	{
	var msg = '';
	if (form.email.value == '')
		msg += "Indicare l'indirizzo email\n";
	var emailPattern = /^[\w-\.]{1,}\@([\da-zA-Z-]{2,}\.){1,}[\da-zA-Z-]{2,4}$/i;
	if (!emailPattern.test(form.email.value))
		msg += "Indirizzo email non corretto\n";
	if (form.nickname.value == '')
		msg += "Indicare il nickname\n";
	if (!form.ho_letto_condizioni_servizio.checked)
		msg += "Indicare di avere letto il regolamento\n";
	if (!form.ho_letto_informativa_privacy.checked)
		msg += "Indicare di avere letto l'informativa sulla privacy\n";
	if (form.captcha.value == '')
		msg += "Indicare il codice di controllo\n";

	if(msg != '')
		{
		alert(msg)
		return false;
		}

	return confirm("Confermi registrazione?");
	}

