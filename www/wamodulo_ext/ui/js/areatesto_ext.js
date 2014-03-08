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
		
		if (tinyMCE.initialized != true)
			{
			tinyMCE.init
				(
					{
					theme : "advanced",
					mode : "specific_textareas",
					plugins : "safari,fullscreen, table",
					//plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
					content_css : "finto.css",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_buttons3_add : "fullscreen,save,cancel",
					theme_advanced_statusbar_location : "none",
					theme_advanced_disable: "styleselect",
					editor_selector: "mceEditor"
					}
				);
			tinyMCE.initialized = true;
			}
			
		setTimeout("document.wapagina.moduli." + this.modulo.nome + ".controlli." + this.nome + ".posiziona()", 200);		
		},
				
	//-------------------------------------------------------------------------
	/**
	* restituisce il  valore applicativo contenuto nel controllo
	*/
	dammiValore: function() 
		{
		tinymce.get(this.nome).save();
		return this.obj.value;
		},
		
	//-----------------------------------------------------------------
	posiziona: function ()
		{
		var tiny_container = document.getElementById(this.nome + "_parent");
		if (!tiny_container)
			setTimeout("document.wapagina.moduli." + this.modulo.nome + ".controlli." + this.nome + ".posiziona()", 100);		
		tiny_container.style.position = "absolute";
		tiny_container.style.top = this.obj.style.top;
		tiny_container.style.left = this.obj.style.left;
		}
		
	}
);



