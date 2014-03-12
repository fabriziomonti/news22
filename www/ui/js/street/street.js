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

//-------------------------------------------------------------------------
function sottoscriviCommentiViaMail(id_articolo)
	{
	
	if (!confirm("Confermi di volere ricevere via email i nuovi commenti su questo articolo di News22?"))
		{
		return false;
		}
		
	location.href = "sottoscrivi.php?azione=sottoscrivi&id_articolo=" + id_articolo;
	}

//-------------------------------------------------------------------------
function smollaCommentiViaMail(id_articolo)
	{
	
	if (!confirm("Confermi di volere terminare di ricevere via email i nuovi commenti su questo articolo di News22?"))
		{
		return false;
		}
		
	location.href = "sottoscrivi.php?azione=smolla&id_articolo=" + id_articolo;
	}

//-------------------------------------------------------------------------
function sottoscriviInterventiViaMail(id_argomento)
	{
	
	if (!confirm("Confermi di volere ricevere via email i nuovi interventi su questo argomento di News22?"))
		{
		return false;
		}
		
	location.href = "sottoscrivi.php?azione=sottoscrivi&id_argomento=" + id_argomento;
	}

//-------------------------------------------------------------------------
function smollaInterventiViaMail(id_argomento)
	{
	
	if (!confirm("Confermi di volere terminare di ricevere via email i nuovi interventi su questo argomento di News22?"))
		{
		return false;
		}
		
	location.href = "sottoscrivi.php?azione=smolla&id_argomento=" + id_argomento;
	}

