//-------------------------------------------------------------------------
function validaForm(form)
	{
	var testo = typeof tinyMCE === 'undefined' ? 
					form.testo.value :
					tinyMCE.get('testo').getContent();

	if (testo.replace(/^\s+|\s+$/g, '') == '')
		return false;

	return confirm("Confermi inserimento commento?");
	}

//-------------------------------------------------------------------------
function rispondiCommento(id_articolo, id_commento)
	{
	var nickname = document.getElementById("nickname_" + id_commento).innerHTML;
	var label = document.getElementById("label_testo_commento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Rispondi a " + nickname;
	form.annulla_modifica.style.visibility = "";
	form.action = "?id_articolo=" + id_articolo + "&id_commento_genitore=" + id_commento;
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
function modificaCommento(id_articolo, id_commento)
	{
	var label = document.getElementById("label_testo_commento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Modifica il commento";
	form.annulla_modifica.style.visibility = "";
	form.action = "?id_articolo=" + id_articolo + "&id_commento=" + id_commento;
	var testo_commento = document.getElementById("commento_" + id_commento).innerHTML;
	if (typeof tinyMCE === 'undefined')
		{
		form.testo.value = testo_commento.replace(/<br>/g, "");
		form.testo.focus();
		}
	else
		{
		tinyMCE.get('testo').setContent(testo_commento);
		form.annulla_modifica.focus();
		tinyMCE.get('testo').focus();
		
		}
	}
	
//-------------------------------------------------------------------------
function annullaModificaCommento(id_articolo)
	{
	var label = document.getElementById("label_testo_commento");
	var form = document.getElementById("wamodulo");
	
	label.innerHTML = "Inserisci un commento";
	form.testo.value = "";
	form.annulla_modifica.style.visibility = "hidden";
	form.action = "?id_articolo=" + id_articolo;
	
	if (tinyMCE && tinyMCE.get('testo'))
		tinyMCE.get('testo').setContent('');

	}

//-------------------------------------------------------------------------
function eliminaCommento(id_articolo, id_commento)
	{
	if (!confirm("Confermi cancellazione commento?"))
		return;
	
	var form = document.getElementById("wamodulo");
	form.onsubmit = "";
	form.action = "?id_articolo=" + id_articolo + "&id_commento=" + id_commento;
	form.wamodulo_operazione.value = "4"; // richiesta cancellazione
	form.submit();
	}

