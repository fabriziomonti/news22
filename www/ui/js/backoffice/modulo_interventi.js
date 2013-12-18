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
	evento_onchange_wamodulo_id_argomento: function (event)
		{
		// quando cambia l'argomento, occorre ricaricare anche i interventi
		// relativi all'argomento
		var ctrl = this.modulo.controlli.id_intervento_genitore;
		ctrl.svuota();
		ctrl.riempi(this.modulo.RPC("rpc_dammiListaInterventi", this.modulo.controlli.id_argomento.dammiValore()));
		}		
	
	
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();
