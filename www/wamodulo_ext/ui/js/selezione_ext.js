//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe waselezione_ext
var waselezione_ext = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'selezione_ext',
	objTesto		: null,
	divLista		: null,
	myonkeyup		: function onkeyup(event) {this.form.wamodulo.controlli[this.name.substr(0, this.name.length - ("_testo").length)].alTastoSu(event);},
	myondblclick	: function ondblclick(event) {this.form.wamodulo.controlli[this.name.substr(0, this.name.length - ("_testo").length)].alDoppioClick(event);},
	myonblur		: function onblur(event) {this.form.wamodulo.controlli[this.name.substr(0, this.name.length - ("_testo").length)].allaPerditaFuoco(event);},
	onkeyup			: false,
	ondblclick		: false,
	onblur			: false,
	
	// elenco dei keycodes che non provocano modifiche del testo in un controllo
	tastiVuoti		: {0:1, 9:1, 13:1, 16:1, 17:1, 18:1, 20:1, 27:1, 33:1, 34:1, 35:1, 36:1, 37:1, 38:1, 39:1, 40:1, 45:1, 144:1},
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.objTesto = this.modulo.obj.elements[this.nome + "_testo"];
		this.divLista = document.getElementById("waselezione_ext_lista_" + this.nome);
		
		this.onkeyup = this.objTesto.onkeyup;
		this.ondblclick = this.objTesto.ondblclick;
		this.onblur = this.objTesto.onblur;
		this.objTesto.onkeyup = this.myonkeyup;
		this.objTesto.ondblclick = this.myondblclick;
		this.objTesto.onblur = this.myonblur;
		},
		
	//-------------------------------------------------------------------------
	// verifica se un controllo fisico appartiene al controllo logico
	miAppartiene: function (obj)
		{
		return obj.name == this.objTesto.name;
		},
	
	//-------------------------------------------------------------------------
	// associa un evento al controllo; se c'e' un evento predefinito lo
	// parcheggia nell'apposita variabile
	aggiungiEvento: function (nomeEvento, evento)
		{
		if (this["my" + nomeEvento])
			this[nomeEvento] = evento;
		else
			this.objTesto[nomeEvento] = evento;
		},
	
	//-------------------------------------------------------------------------
	renderizza: function() 
		{
		this.objTesto.className = (this.obbligatorio ? "wamodulo_obbligatorio" : '');
		if (this.objTesto.value != '' && this.obj.value == '')
			this.objTesto.className += (this.objTesto.className ? ' ' : '') + "wamodulo_selezione_ext_non_trovato";
			
		this.divLista.style.visibility = this.divLista.innerHTML == '' || this.obj.value != '' ? 'hidden' : '';
		},
		
	//-------------------------------------------------------------------------
	mettiValore: function(valoreChiave, valoreTesto) 
		{
		this.obj.value = valoreChiave;
		this.objTesto.value = valoreTesto;
			
		this.renderizza();
		},
		
	//-------------------------------------------------------------------------
	// simula un evento sul controllo
	simulaEvento: function(tipoEvento) 
		{
		var event;
		if (document.createEvent) 
			{
			event = document.createEvent("HTMLEvents");
			event.initEvent(tipoEvento, true, true);
			this.objTesto.dispatchEvent(event);
			} 
		else 
			{
			event = document.createEventObject();
			event.eventType = tipoEvento;
			this.objTesto.fireEvent("on" + event.eventType, event);
			}
		
		},
		
	//-------------------------------------------------------------------------
	testa: function(messaggio) 
		{
		alert(messaggio);
		},
		
	//-------------------------------------------------------------------------
	alTastoSu: function(event) 
		{
			
		if(window.event)
			event = window.event;
		if (event && !this.tastiVuoti[event.keyCode])
			{
			var modulo = this.modulo.nome;
			var controllo = this.nome;
			var testoDaCercare = this.objTesto.value;
			setTimeout(function() {document[modulo].controlli[controllo].dammiLista(event, testoDaCercare)}, 500);
			this.eventoApplicazione(event, "onkeyup", this.objTesto);
			}
		},
		
	//-------------------------------------------------------------------------
	// effettua la chiamata rpc per farsi restituire la lista delle opzioni da 
	// cui scegliere il valore del controllo
	dammiLista: function(event, testoDaCercare) 
		{
		// se il campo testo nel frattempo e' cambiato non facciamo la ricerca
		if (this.objTesto.value != testoDaCercare)
			return;
		
		this.obj.value = '';
		
		// passiamo al server tutti i valori già imputati nel modulo, in modo che
		// possa prendere decisioni sulla lista da ritornare (dipndenze 
		// applicative, ecc.)
		var valoriModulo = '{';
		for (var li in this.modulo.controlli)
			{
			valoriModulo += '"' + li + '":"' + this.modulo.controlli[li].dammiValore() + '",';
			}
		// aggiungiam a valoriModulo anche il testo da ricercare
		valoriModulo += '"' + this.nome + '_testo":"' + this.objTesto.value + '"';
		valoriModulo += '}';
		// chiamiamo il metodo RPC lato server
		var esito = this.modulo.RPC("rpc_ricarica_opzioni_" + this.nome, valoriModulo);
		
	    if (esito == this.modulo.errore_rpc)
	     	return esito;

		var div_content = '';
		var cntr = 0;
		for (var key in esito)
			{
			div_content += '<a href="javascript:document.' + this.modulo.nome + '.controlli.' + this.nome + '.seleziona(' +
									"'" + key + "', " +
									"'" + (esito[key]).replace(/\'/g,"\\\'") + "')\"" +
									' id="' + this.nome + "_opzione_" + key + '"' +
									'>' + 
									esito[key] + "</a>\n";
			if (cntr == 0 && (esito[key]).toLowerCase() == this.objTesto.value.toLowerCase())
				this.obj.value = key;
			cntr++;
			}
			
		this.divLista.innerHTML = div_content;
		this.renderizza();

		// se il testo viene modificato simuliamo la onchange
		if (event && !this.tastiVuoti[event.keyCode])
			this.simulaEvento("change");
		},

	//-------------------------------------------------------------------------
	alDoppioClick: function(event) 
		{
		if (this.objTesto.value != '')
			return;
		var salvaFG = this.objTesto.style.color;
		this.objTesto.style.color = this.objTesto.style.backgroundColor;
		this.objTesto.value = '%';
		this.dammiLista(null, this.objTesto.value);
		this.objTesto.value = '';
		this.objTesto.style.color = '';

		this.eventoApplicazione(event, "ondblclick", this.objTesto);
		},
		
	
	//-------------------------------------------------------------------------
	allaPerditaFuoco: function(event) 
		{
		setTimeout("document." + this.modulo.nome + ".controlli." + this.nome +  ".allaPerditaFuocoRitardato()", 100);
		this.eventoApplicazione(event, "onblur", this.objTesto);
		},
		
	//-------------------------------------------------------------------------
	seleziona: function(chiave, testo) 
		{
		this.obj.value = chiave;
		this.objTesto.value = testo;
		
		this.divLista.innerHTML = '';
		this.renderizza();
		this.simulaEvento("change");
		},
		
	//-------------------------------------------------------------------------
	allaPerditaFuocoRitardato: function() 
		{
		if (document.activeElement && document.activeElement.id &&  document.activeElement.id.substr(0, (this.nome + "_opzione_").length) == this.nome + "_opzione_")
			return;
		this.divLista.innerHTML = '';
		this.renderizza();
		}
		
	}
);



