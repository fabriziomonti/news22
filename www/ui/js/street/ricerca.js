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

