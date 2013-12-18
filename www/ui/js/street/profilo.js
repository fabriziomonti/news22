//-------------------------------------------------------------------------
function validaForm(form)
	{
	var msg = '';
	if (form.email.value == '')
		msg += "Indicare l'indirizzo email\n";
	var emailPattern = /^[\w-\.]{1,}\@([\da-zA-Z-]{2,}\.){1,}[\da-zA-Z-]{2,4}$/i;
	if (!emailPattern.test(form.email.value))
		msg += "Indirizzo email non corretto\n";
	msg += verificaPassword (form.pwd.value, form.pwd.pwd_conferma);

	if(msg != '')
		{
		alert(msg)
		return false;
		}

	return confirm("Confermi registrazione?");
	}

//-------------------------------------------------------------------------
function verificaPassword (pwd, pwd2)
	{
	if (pwd == '')
		return '';

	if (pwd.length < 8)
		return ("La password deve essere almeno di 8 caratteri\n");
	if (pwd.length > 12)
		return ("La password deve essere al massimo di 12 caratteri\n");

	if (pwd.match(new RegExp("[^A-Za-z0-9]")))
		return ("La password contiene caratteri non validi\n");

	if (!pwd.match(new RegExp("[A-Z]")))
		return ("La password deve contenere almeno una lettera maiuscola\n");

	if (!pwd.match(new RegExp("[a-z]")))
		return ("La password deve contenere almeno una lettera minuscola\n");

	if (!pwd.match(new RegExp("[0-9]")))
		return ("La password deve contenere almeno un numero\n");

	if (pwd2 && (pwd != pwd2))
		return ("Le password non corrispondono\n");

	return '';
	}

//-------------------------------------------------------------------------
function confermaEliminaUtenza()
	{
	if (!confirm("Confermi cancellazione utenza?"))
		return;
	
	if (!confirm("Non lo prendo come un fatto personale, ma tu sei proprio sicuro sicuro?"))
		return;
	
	var form = document.getElementById("wamodulo");
	form.onsubmit = "";
	form.wamodulo_operazione.value = "4"; // richiesta cancellazione
	form.submit();
	}

