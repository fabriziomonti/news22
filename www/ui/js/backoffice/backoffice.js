//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var backoffice = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: news22,

	//-------------------------------------------------------------------------
	// proprieta'
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function()
		{
		this.parent();
				
		document.onkeyup = function onkeyup(event) {document.wapagina.intercettaEsc(event);}
		},
	
	//-------------------------------------------------------------------------
	// ridefiniamo la apri pagina per usare jquery e fancybox
	apriPagina: function (pagina)
	    {
		this.hoUnaPaginaAPerta = true;
		
		if (this.modalitaNavigazione == this.modalitaNavigazioneFinestra)
			return this.parent(pagina);
		
		jQuery("a.iframe").fancybox(
								{
								href : pagina,
								type : 'iframe',
								hideOnOverlayClick: false,
								width: 1024,
								height: 800,
								speedIn	:	0, 
								speedOut :	0,
								padding: 0,
								margin: 20,
								onCleanup: function() 
											{
											if (self.frames[0] && 
													self.frames[0].document.wapagina &&
													self.frames[0].document.wapagina.chiudiPagina)
												return self.frames[0].document.wapagina.chiudiPagina();
											}
								}
								);
	
			jQuery("a.iframe").click();

		},
	    
	//-------------------------------------------------------------------------
	// ridefiniamo la chiudi pagina per usare jquery e fancybox
	chiudiPagina: function ()
	    {
		w = opener ? opener : parent;
		w.document.wapagina.hoUnaPaginaAPerta = false;
		if (this.modalitaNavigazione == this.modalitaNavigazioneFinestra)
			return this.parent();

		parent.jQuery.fancybox.close();
		return true;
		},
	    
	//-------------------------------------------------------------------------
	intercettaEsc: function (event)
	    {
			
		event = event ? event : window.event;
		if (event.keyCode == 27)
			{
			if (this.modulo && this.modulo.controlli && this.modulo.controlli.cmd_annulla)
				this.modulo.controlli.cmd_annulla.obj.click();
			if (this.tabella && document.forms[this.tabella.nome + "_bottoniera"].Chiudi)
				document.forms[this.tabella.nome + "_bottoniera"].Chiudi.click();
				
			}
		},
		
	//-------------------------------------------------------------------------
	// azione di chiusura delle pagine figlie con tabelle
	azione_watabella_Chiudi: function ()
	    {
	    this.chiudiPagina();
	    },
	    
	//-------------------------------------------------------------------------
	// azione di export pdf
	azione_watabella_esportapdf: function ()
	    {
	    var w = open(location.href + (location.href .indexOf("?") != -1 ? "&" : "?") + "watbl_esporta_pdf[watabella]=1");
	    },
	    
	//-------------------------------------------------------------------------
	// ovviamente questo metodo viene richiamato solo in situazioni standard
	// ossia quando c'e' un modulo che si chiama wamodulo e un bottone di
	// annullamento dell'editing che si chiama cmd_annulla; se siete in una
	// altra situzione dovete implementare la vostra gestione dell'evento
	evento_onclick_wamodulo_cmd_annulla: function (event)
	    {
	    this.chiudiPagina();
	    },
	    
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	// metodi per la gestione del popolamento dei controlli selezione/selezione_ext 
	// (bottoni nuovo/modifica)
	
	//-------------------------------------------------------------------------
	// da richiamare quando una selezione cambia il proprio valore: abilita
	// o disabilita il bottone di modifica dell'elemento
	selezione_change: function (nome_selezione)
		{
		this.modulo.controlli["btn_modifica_" + nome_selezione].abilita(
							this.modulo.controlli[nome_selezione].dammiValore()
							);
		},
		
	//-------------------------------------------------------------------------
	selezione_btn_nuovo_click: function (nome_selezione, nome_modulo, nome_dipende_da)
		{
		this.metodoAllineamento = this["allinea_" + nome_selezione];
		var pagina = nome_modulo + ".php?" + this.chiaveOperazione + "=" + this.opeInserimento;
		if (nome_dipende_da)
			pagina += "&" + nome_dipende_da + "=" + this.modulo.controlli[nome_dipende_da].dammiValore();
		this.apriPagina(pagina);
		},
		
	//-------------------------------------------------------------------------
	selezione_btn_modifica_click: function (nome_selezione, nome_modulo)
		{
		this.metodoAllineamento = this["allinea_" + nome_selezione];
		this.apriPagina(nome_modulo + ".php?" + this.chiaveOperazione + "=" + this.opeModifica +
							"&"  + nome_selezione + "=" + this.modulo.controlli[nome_selezione].dammiValore());
		},
		
	//-------------------------------------------------------------------------
	selezione_allinea: function (nome_selezione, datiInputFiglia, nome_funzione_rpc)
		{
		var ctrl = this.modulo.controlli[nome_selezione];
		var lista = this.modulo.RPC(nome_funzione_rpc);
		var nuovoValore;
		if (datiInputFiglia.cmd_invia)
			{
			var nuovoValore = datiInputFiglia.idInserito ? datiInputFiglia.idInserito : ctrl.dammiValore();
			}
		else
			{
			nuovoValore = '';
			}
		
		ctrl.svuota();
		ctrl.riempi(lista, nuovoValore);

		// simuliamo la onchange, in modo che il bottone di modifica si
		// abiliti/disabiliti a seconda della situazione
		ctrl.simulaEvento("change");
		},
		
	//-------------------------------------------------------------------------
	// è differente dalla selezione normale in quanto
	// - la selezione normale deve ricaricare la lista lato server tramite RPC
	// - la selezione_ext preleva i due valori (id e testo) direttamente dai dati ricevuti lato client
	selezione_ext_allinea: function (nome_selezione, datiInputFiglia)
		{
		var ctrl = this.modulo.controlli[nome_selezione];
		if (datiInputFiglia.cmd_invia)
			{		
			ctrl.mettiValore(datiInputFiglia.idInserito ? datiInputFiglia.idInserito : ctrl.dammiValore(), datiInputFiglia.nome);
			}
		else
			{
			ctrl.mettiValore('', '');
			}
			
		// simuliamo la onchange, in modo che il bottone di modifica si
		// abiliti/disabiliti a seconda della situazione
		ctrl.simulaEvento("change");
		},
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	// fine metodi per la gestione del popolamento dei controlli selezione/selezione_ext 
	// (bottoni nuovo/modifica)
	//-------------------------------------------------------------------------
		
	//-------------------------------------------------------------------------
	// metodi per la visualizzazione inline dell'html di una tabella
	//-------------------------------------------------------------------------
	azione_watabella_toggle_HTML: function (on_off)
		{
		var qs = this.rimuoviParametroDaQS("html");
		var qoe = qs.substr(0, 1) == "?" ? "&" : "?";
		location.href = qs + qoe + "html=" + on_off;
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_HTML: function ()
		{
		this.azione_watabella_toggle_HTML(1);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_NO_HTML: function ()
		{
		this.azione_watabella_toggle_HTML(0);
		}
		
	    
	    
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------




