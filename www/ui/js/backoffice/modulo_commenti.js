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
	evento_onchange_wamodulo_id_articolo: function (event)
		{
		// quando cambia l'articolo, occorre ricaricare anche i commenti
		// relativi all'articolo
		var ctrl = this.modulo.controlli.id_commento_genitore;
		ctrl.svuota();
		ctrl.riempi(this.modulo.RPC("rpc_dammiListaCommenti", this.modulo.controlli.id_articolo.dammiValore()));
		}		
	
	
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();
