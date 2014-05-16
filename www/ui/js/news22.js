//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var news22 = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: waapplicazione,

	//-------------------------------------------------------------------------
	// proprieta'
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function()
		{
		this.parent();
		this.fuocoAlPrimoControllo();
				
		},
	
	//-------------------------------------------------------------------------
	fuocoAlPrimoControllo: function ()
	    {
		// da non fare in caso di mobile...
		if (this.isTouchDevice())
			return ;
		
		// se la pagina ha una form e se la form ha un controllo di tipo testo
		// diamo il focus al primo controllo di tipo testo
		var focusForm;
		if (this.modulo && this.modulo.nome != 'moduloSceltaStruttura')
			focusForm = this.modulo.obj;
		else if(this.tabella)
			focusForm = document.getElementById(this.tabella.nome + "_bottoniera");
	
		if (!focusForm)
			return;
			
		for (var li = 0; li < focusForm.elements.length; li++)
			{
			if (focusForm.elements[li].type.toLowerCase() != 'hidden' &&
				focusForm.elements[li].style.visibility.toLowerCase() != 'hidden' &&
				focusForm.elements[li].disabled != true && 
				focusForm.elements[li].type.toLowerCase() != 'button' && 
				focusForm.elements[li].type.toLowerCase() != 'select-one')
				{
				focusForm.elements[li].focus();
				break;
				}
					
			}
	    },
	    
	//-------------------------------------------------------------------------
	isTouchDevice: function ()
		{
		var msTouchEnabled = window.navigator.msMaxTouchPoints;
		var generalTouchEnabled = "ontouchstart" in document.createElement("div");
		return msTouchEnabled || generalTouchEnabled;
		},


	//-------------------------------------------------------------------------
	verificaPassword: function (pwd, pwd2)
	    {
	    if (pwd.length < 8)
	        return this.msgErrore("La password deve essere almeno di 8 caratteri");
	    if (pwd.length > 12)
	        return this.msgErrore("La password deve essere al massimo di 12 caratteri");
	       
	    if (pwd.match(new RegExp("[^A-Za-z0-9]")))
	        return this.msgErrore("La password contiene caratteri non validi");
	   
	    if (!pwd.match(new RegExp("[A-Z]")))
	        return this.msgErrore("La password deve contenere almeno una lettera maiuscola");
	   
	    if (!pwd.match(new RegExp("[a-z]")))
	        return this.msgErrore("La password deve contenere almeno una lettera minuscola");
	   
	    if (!pwd.match(new RegExp("[0-9]")))
	        return this.msgErrore("La password deve contenere almeno un numero");
	   
	    if (pwd2 && (pwd != pwd2))
			return this.msgErrore("Le password non corrispondono");
	       
	    return true;
	    }
	    
		
	    
	    
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------




