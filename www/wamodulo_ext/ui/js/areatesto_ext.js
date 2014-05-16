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
			tinyMCE.init(
							{
							relative_urls : true,
							document_base_url : "./",
							forced_root_block : false,
							force_br_newlines : true,
							force_p_newlines : false,
							selector : ".mceEditor",
							plugins : "fullscreen, link, image, textcolor, emoticons, table, code, media, template, hr",
							menu :	
								{ 
								edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
								insert : {title : 'Insert', items : 'link image media | hr'},
								format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats removeformat'},
								table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
								tools  : {title : 'Tools' , items : 'fullscreen code'}
								},

							toolbar: "fontselect fontsizeselect bullist numlist outdent indent forecolor backcolor emoticons fullscreen",

							readonly : false,
							statusbar : false,
							setup: function(editor) 
								{
//								editor.on('BeforeRenderUI', function(e) 
//									{
//									this.settings.width = this.getElement().style.width;
//									this.settings.height = this.getElement().style.height;
//									});

								}

							}			
						);
			tinyMCE.initialized = true;
			}
			
		},
				
	//-------------------------------------------------------------------------
	/**
	* restituisce il  valore applicativo contenuto nel controllo
	*/
	dammiValore: function() 
		{
		tinymce.get(this.nome).save();
		return this.obj.value;
		}
		

	}
);



