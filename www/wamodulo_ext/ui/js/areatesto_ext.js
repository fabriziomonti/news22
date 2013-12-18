//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe waareatesto_ext
var waareatesto_ext = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: waareatesto,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'areatesto_ext',
	
	//-------------------------------------------------------------------------
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		
		setTimeout("document.wapagina.moduli." + this.modulo.nome + ".controlli." + this.nome + ".posiziona()", 200);		
		},
				
	//-----------------------------------------------------------------
	posiziona: function ()
		{
		var tiny_container = document.getElementById(this.nome + "_parent");
		tiny_container.style.position = "absolute";
		tiny_container.style.top = this.obj.style.top;
		tiny_container.style.left = this.obj.style.left;
		}
		
	}
);



