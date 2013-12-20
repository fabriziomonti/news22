//-------------------------------------------------------------------------
function validaForm(form)
	{
	var testo = typeof tinyMCE === 'undefined' ? 
					form.testo.value :
					tinyMCE.get('testo').getContent();

	if (testo.replace(/^\s+|\s+$/g, '') == '')
		return false;

	return confirm("Confermi inserimento intervento?");
	}

//-------------------------------------------------------------------------
function rispondiIntervento(id_argomento, id_intervento)
	{
	var nickname = document.getElementById("nickname_" + id_intervento).innerHTML;
	var label = document.getElementById("label_testo_intervento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Rispondi a " + nickname;
	form.annulla_modifica.style.visibility = "";
	form.action = "?id_argomento=" + id_argomento + "&id_intervento_genitore=" + id_intervento;
	if (typeof tinyMCE === 'undefined')
		{
		form.testo.focus();
		}
	else
		{
		form.annulla_modifica.focus();
		tinyMCE.get('testo').focus();
		
		}
	}
	
//-------------------------------------------------------------------------
function modificaIntervento(id_argomento, id_intervento)
	{
	var label = document.getElementById("label_testo_intervento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Modifica l'intervento";
	form.annulla_modifica.style.visibility = "";
	form.action = "?id_argomento=" + id_argomento + "&id_intervento=" + id_intervento;
	var testo_intervento = document.getElementById("intervento_" + id_intervento).innerHTML;
	if (typeof tinyMCE === 'undefined')
		{
		form.testo.value = testo_intervento.replace(/<br>/g, "");
		form.testo.focus();
		}
	else
		{
		tinyMCE.get('testo').setContent(testo_intervento);
		form.annulla_modifica.focus();
		tinyMCE.get('testo').focus();
		
		}
	}
	
//-------------------------------------------------------------------------
function annullaModificaIntervento(id_argomento)
	{
	var label = document.getElementById("label_testo_intervento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Inserisci un intervento";
	form.testo.value = "";
	form.annulla_modifica.style.visibility = "hidden";
	form.action = "?id_argomento=" + id_argomento;

	if (tinyMCE && tinyMCE.get('testo'))
		tinyMCE.get('testo').setContent('');
	}

//-------------------------------------------------------------------------
function eliminaIntervento(id_argomento, id_intervento)
	{
	if (!confirm("Confermi cancellazione intervento?"))
		return;
	
	var form = document.getElementById("wamodulo");
	form.onsubmit = "";
	form.action = "?id_argomento=" + id_argomento + "&id_intervento=" + id_intervento;
	form.wamodulo_operazione.value = "4"; // richiesta cancellazione
	form.submit();
	}

