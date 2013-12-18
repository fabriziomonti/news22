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
	evento_onsubmit_wamodulo_wamodulo: function (event)
		{
		if(this.modulo.controlli.pwd &&
			(!this.verificaPassword(this.modulo.controlli.pwd.dammiValore(), this.modulo.controlli.pwd_conferma.dammiValore()))
			)
			{
			return false;
			}
			
		return this.modulo.validaModulo();
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_wamodulo_supervisore: function (event)
		{
			
		this.modulo.controlli.id_privilegio.mettiValore([]);
		this.modulo.controlli.id_privilegio.abilita(!this.modulo.controlli.supervisore.dammiValore(), true);
		}
	
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();
