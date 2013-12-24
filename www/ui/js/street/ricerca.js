//-------------------------------------------------------------------------
function checkData(data_data)
	{
		
	if (data_data == '')
		return true;
	
	var elems = data_data.split("/");
	var data = new Date(elems[2], elems[1] - 1, elems[0]);
	var ridata = pad(data.getDate(), 2, 0) + "/" + 
					 pad(data.getMonth() + 1, 2, 0) + "/" + 
					 data.getFullYear();
		 
	return ridata == data_data;
	}
//-------------------------------------------------------------------------
function validaForm(form)
	{
	var msg = '';

	if (!checkData(form.dalla_data.value))
		msg += "Campo 'Dalla data' non valido (dd/mm/YYYY)\n";
	if (!checkData(form.alla_data.value))
		msg += "Campo 'Alla data' non valido (dd/mm/YYYY)\n";
	
	if(msg != '')
		{
		alert(msg)
		return false;
		}
		
	return true;
	}

//-------------------------------------------------------------------------
function tutte_categorie_articoli_click(ctrl)
	{
	
	var current = null;
	for (var li = 0; li < ctrl.form.elements.length; li++)
		{
			
		current = ctrl.form.elements[li];
		
		if (current.name.indexOf("id_categoria_articolo[") == 0)
			{
			current.checked = false;
			current.disabled = ctrl.checked;
			}
		}
		
	}

//-------------------------------------------------------------------------
function tutte_categorie_argomenti_click(ctrl)
	{
	
	var current = null;
	for (var li = 0; li < ctrl.form.elements.length; li++)
		{
			
		current = ctrl.form.elements[li];
		
		if (current.name.indexOf("id_categoria_argomento[") == 0)
			{
			current.checked = false;
			current.disabled = ctrl.checked;
			}
		}
		
	}

