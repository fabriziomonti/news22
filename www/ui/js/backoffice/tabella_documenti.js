//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var wapagina = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: backoffice,

	//-------------------------------------------------------------------------
	// proprieta'

	//-------------------------------------------------------------------------
	//initialization  
	// la fa il parent
			
	//-------------------------------------------------------------------------
	link_watabella_url: function (id)
		{
		var w = window.open(this.tabella.righe[id].campi.url);
		},
		
	//-------------------------------------------------------------------------
	link_watabella_link: function (id)
		{
		var w = window.open("../downloaddoc.php?tabella=documenti&tipo=nome&id=" + id);
		}
		
	
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();
