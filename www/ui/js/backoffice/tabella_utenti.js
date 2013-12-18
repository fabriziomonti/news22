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
	link_watabella_email: function (id)
		{
		location.href = "mailto:" + this.tabella.righe[id].campi.email;
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Articoli: function (id)
		{
		this.apriPagina("tabella_articoli.php?id_utente=" + id);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Commenti: function (id)
		{
		this.apriPagina("tabella_commenti.php?id_utente=" + id);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Argomenti: function (id)
		{
		this.apriPagina("tabella_argomenti.php?id_utente=" + id);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Interventi: function (id)
		{
		this.apriPagina("tabella_interventi.php?id_utente=" + id);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Documenti: function (id)
		{
		this.apriPagina("tabella_documenti.php?id_utente=" + id);
		}
		
	
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();
