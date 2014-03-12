<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" 
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				xmlns:str="http://exslt.org/strings" exclude-result-prefixes="str"
				xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl"
>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 


<!-- *********************************************************************** -->
<!--  template head html -->
<!-- *********************************************************************** -->
<xsl:template name="pagina_head_html">

	<xsl:param name="id_articolo" />
	<xsl:param name="id_argomento" />

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><xsl:value-of select="/waapplicazione/titolo"/></title>
		<link rel="alternate" title="{/waapplicazione/titolo} - articoli" href="../web_files/rss/articoli.xml" type="application/rss+xml" />
		<xsl:if test="$id_articolo">
			<!--siamo nella pagina dell'articolo: occorre aggiungere anche il link -->
			<!--all'rss dei commenti-->
			<link rel="alternate" title="{/waapplicazione/titolo} - commenti" href="../web_files/rss/commenti.{$id_articolo}.xml" type="application/rss+xml" />
		</xsl:if>
		<xsl:if test="$id_argomento">
			<!--siamo nella pagina dell'argomento del forum: occorre aggiungere anche il link -->
			<!--all'rss degli interventi-->
			<link rel="alternate" title="{/waapplicazione/titolo} - interventi" href="../web_files/rss/interventi.{$id_argomento}.xml" type="application/rss+xml" />
		</xsl:if>
		<link rel="shortcut icon" href="../ui/img/street/favicon.ico" type="image/x-icon" />

		<!--caricamento dei css specifici di applicazione e pagina-->
		<xsl:variable name="ui_dir" select="/waapplicazione/pagina/elementi/elemento[nome='ui_dir']/valore" />
		<link href="{$ui_dir}/css/street/street.css" rel="stylesheet" type="text/css" />
		<link href="{$ui_dir}/css/street/{pagina/nome}.css" rel="stylesheet" type="text/css" /><xsl:text>&#10;</xsl:text>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script><xsl:text>&#10;</xsl:text>
	</head>
</xsl:template>

<!-- *********************************************************************** -->
<!--  template header -->
<!-- *********************************************************************** -->
<xsl:template name="pagina_header">
			
	<xsl:variable name="dati_utente" select="exsl:node-set(pagina/elementi/elemento[nome='dati_utente']/valore)" />

	<div id="header">
		&nbsp;			
	</div>

	<!-- BLOCCO MENU -->
	<div id="bloccoMenu">
	
		<div id="">
			<xsl:variable name="classe_css" >
				<xsl:choose>
					<xsl:when test="contains(/waapplicazione/pagina/uri, 'index.php') and not(contains(/waapplicazione/pagina/uri, 'id_categoria_articolo='))" >
						<xsl:text>btn_menuCategorieArticoli_selezionato</xsl:text>
					</xsl:when>
					<xsl:otherwise>
							<xsl:text>btn_menuCategorieArticoli</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<a href="index.php" class="{$classe_css}">
				Il Blog
			</a>
		</div>

		<!-- MENU CATEGORIE ARTICOLI BLOG  -->
		<div id="menuCategorieArticoli">
			<xsl:text>&#10;</xsl:text>
			<xsl:for-each select="pagina/elementi/elemento[nome='categorie_articoli']/valore/riga">
				<xsl:variable name="classe_css" >
					<xsl:choose>
						<xsl:when test="contains(/waapplicazione/pagina/uri, concat('id_categoria_articolo=', id_categoria_articolo))" >
							<xsl:text>btn_menuCategorieArticoli_selezionato</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>btn_menuCategorieArticoli</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<a href="index.php?id_categoria_articolo={id_categoria_articolo}" class="{$classe_css}">
					<xsl:value-of select="nome"/>
				</a>
				<xsl:text>&#10;</xsl:text>
			</xsl:for-each>	
		</div><!-- id="menuCategorieArticoli" -->
		

		<!-- MENU CATEGORIE ARGOMENTI FORUM  -->
		<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome='categorie_argomenti']/valore/riga" >
			<div id="intestazioneForum">
				<xsl:variable name="classe_css" >
					<xsl:choose>
						<xsl:when test="contains(/waapplicazione/pagina/uri, 'forum.php') and not(contains(/waapplicazione/pagina/uri, 'id_categoria_argomento='))" >
							<xsl:text>btn_menuCategorieArgomenti_selezionato</xsl:text>
						</xsl:when>
						<xsl:otherwise>
								<xsl:text>btn_menuCategorieArgomenti</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<a href="forum.php" class="{$classe_css}">
					Il Forum
				</a>
			</div>

			<div id="menuCategorieArgomenti">
				<xsl:for-each select="pagina/elementi/elemento[nome='categorie_argomenti']/valore/riga">
					<xsl:variable name="classe_css" >
						<xsl:if test="contains(/waapplicazione/pagina/uri, concat('id_categoria_argomento=', id_categoria_argomento))" >
							<xsl:text>btn_menuCategorieArgomenti_selezionato</xsl:text>
						</xsl:if>
						<xsl:if test="not(contains(/waapplicazione/pagina/uri, concat('id_categoria_argomento=', id_categoria_argomento)))" >
							<xsl:text>btn_menuCategorieArgomenti</xsl:text>
						</xsl:if>
					</xsl:variable>
					<a href="forum.php?id_categoria_argomento={id_categoria_argomento}" class="{$classe_css}">
						<xsl:value-of select="nome"/>
					</a>
				</xsl:for-each>	
			</div> <!-- id="menuCategorieArgomenti" -->
		</xsl:if>


	</div><!-- id="bloccoMenu" -->
	
	<!-- BLOCCO RICERCA -->
	<div id="bloccoRicerca">
		<xsl:if test="not($dati_utente)">
			<a href="login.php">Accedi</a> / <a href="registrazione.php">Registrati</a>
		</xsl:if>
		<xsl:if test="$dati_utente">
			Ciao <xsl:value-of select="$dati_utente/nickname"/>
			<br />
			<a href="profilo.php">Il tuo profilo</a> 
			|
			<xsl:if test="$dati_utente/supervisore= 1 or $dati_utente/privilegi/elemento_id_1 = 1">
				<a href="../backoffice">Backoffice</a> 
				|
			</xsl:if>
			<a href="logout.php">Esci</a> 
		</xsl:if>
		<form action="index.php">
			<input type="button" value="Cerca" name="bottone_ricerca_rapida"/>
			<xsl:text> </xsl:text>
			<input type="text" name="ricerca_libera" /><br />
			<a href="ricerca.php">Ricerca avanzata</a>
			<!--<a href="javascript:alert('scherzetto! la ghe sta no!')">Ricerca avanzata</a>-->
		</form>

	</div><!-- id="bloccoRicerca" -->

	<!--display eventuale messaggio di errore-->
	<xsl:call-template name="messaggio_errore" >
		<xsl:with-param name="messaggio" select="pagina/elementi/elemento[nome='messaggio']/valore" />
	</xsl:call-template>

	<!--display eventuale messaggio ok -->
	<xsl:call-template name="messaggio_ok" >
		<xsl:with-param name="messaggio" select="pagina/elementi/elemento[nome='messaggio_ok']/valore" />
	</xsl:call-template>

</xsl:template>
					
<!-- *********************************************************************** -->
<!--  template footer -->
<!-- *********************************************************************** -->
<xsl:template name="pagina_footer">
	<xsl:param name="id_articolo" />
	<xsl:param name="id_argomento" />
	
	<xsl:variable name="dati_utente" select="exsl:node-set(/waapplicazione/pagina/elementi/elemento[nome='dati_utente']/valore)" />
	
	<div id="footer">
		<a name="footer"></a>
		<img src="../ui/img/street/rss.png" onclick="location.href='../web_files/rss/articoli.xml'" border="0" alt="Articoli di {/waapplicazione/titolo}" title="Articoli di {/waapplicazione/titolo}" />
		<xsl:if test="$id_articolo">
			<!--siamo nella pagina dell"articolo: occorre aggiungere anche il link all'rss dei commenti-->
			<img src="../ui/img/street/rss.png" border="0" onclick="location.href='../web_files/rss/commenti.{$id_articolo}.xml'" alt="Commenti all'articolo di {/waapplicazione/titolo}" title="Commenti all'articolo di {/waapplicazione/titolo}" />
			<xsl:if test="$dati_utente/id_utente != ''">
				<!-- se l'utente è loggato diamo anche l'icona per la sottoscrizione via email -->
				<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome = 'articolo']/valore/riga/flag_sottoscrizione_via_email = 0">
					<img src="../ui/img/street/email.png" onclick="sottoscriviCommentiViaMail({$id_articolo})" alt="Ricevi via email i commenti di questo articolo di {/waapplicazione/titolo}" title="Ricevi via email i commenti di questo articolo di {/waapplicazione/titolo}" />
				</xsl:if>
				<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome = 'articolo']/valore/riga/flag_sottoscrizione_via_email = 1">
					<img src="../ui/img/street/noemail.png" onclick="smollaCommentiViaMail({$id_articolo})" alt="Termina di ricevere via email i commenti di questo articolo di {/waapplicazione/titolo}" title="Termina di ricevere via email i commenti di questo articolo di {/waapplicazione/titolo}" />
				</xsl:if>
			</xsl:if>
		</xsl:if>
		<xsl:if test="$id_argomento">
			<!--siamo nella pagina dell'argomento del forum: occorre aggiungere anche il link all'rss degli interventi-->
			<img src="../ui/img/street/rss.png" onclick="location.href='../web_files/rss/interventi.{$id_argomento}.xml'" border="0" alt="Interventi di {/waapplicazione/titolo}" title="Interventi di {/waapplicazione/titolo}" />
			<xsl:if test="$dati_utente/id_utente != ''">
				<!-- se l'utente è loggato diamo anche l'icona per la sottoscrizione via email -->
				<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome = 'argomento']/valore/riga/flag_sottoscrizione_via_email = 0">
					<img src="../ui/img/street/email.png" onclick="sottoscriviInterventiViaMail({$id_argomento})" alt="Ricevi via email gli interventi di questo argomento di {/waapplicazione/titolo}" title="Ricevi via email i commenti di questo argomento di {/waapplicazione/titolo}" />
				</xsl:if>
				<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome = 'argomento']/valore/riga/flag_sottoscrizione_via_email = 1">
					<img src="../ui/img/street/noemail.png" onclick="smollaInterventiViaMail({$id_argomento})" alt="Termina di ricevere via email gli interventi di questo argomento di {/waapplicazione/titolo}" title="Termina di ricevere via email gli interventi di questo argomento di {/waapplicazione/titolo}" />
				</xsl:if>
			</xsl:if>
		</xsl:if>
	</div><!-- footer -->

	<!--caricamento javascript vari-->
	<xsl:if test="/waapplicazione/pagina/elementi/elemento[nome='dati_utente']/valore/privilegio_html_esteso = 1">	
		<script type='text/javascript' src='../walibs3/wadocumentazione/wadocapp/ui/js/tiny_mce/tiny_mce.js' />
		<xsl:text>&#10;</xsl:text>
		<script type="text/javascript">
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
		</script>
	</xsl:if><!--html esteso-->

	<script type="text/javascript" src="../walibs3/waapplicazione/uis/wa_default/js/strmanage.js" />

	<!--caricamento dei javascript specifici di applicazione e pagina-->
	<xsl:variable name="ui_dir" select="/waapplicazione/pagina/elementi/elemento[nome='ui_dir']/valore" />
	<script type="text/javascript" src="{$ui_dir}/js/street/street.js" />
	<script type="text/javascript" src="{$ui_dir}/js/street/{pagina/nome}.js" />

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="blocco_articolo">
	<xsl:param name="articolo"/>
	<xsl:param name="tipo_posizione"/>
	<xsl:param name="flag_testo"/>

	<h1 class="{$tipo_posizione}_titolo">
		<a href='articolo.php?id_articolo={$articolo/id_articolo}'>
			<xsl:value-of disable-output-escaping="yes" select="$articolo/titolo"/>
		</a>
	</h1>

	<xsl:if test="$articolo/abstract != ''">
		<div class="{$tipo_posizione}_sintesi">
			<xsl:value-of disable-output-escaping="yes" select="$articolo/abstract"/>
		</div>
	</xsl:if>
	<xsl:if test="$flag_testo = 1">
		<div>
			<xsl:value-of disable-output-escaping="yes" select="$articolo/testo"/>
		</div>
	</xsl:if>
	<xsl:call-template name="dati_articolo">
		<xsl:with-param name="articolo" select="$articolo" />
	</xsl:call-template>

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="dati_articolo">
	<xsl:param name="articolo"/>

	<div class="datiArticolo">
		[ Pubblicato da: 
		<a href='vedi_utente.php?id_utente={$articolo/id_utente}'>
			<xsl:value-of select="$articolo/nickname"/>
		</a>
		il: <xsl:value-of select="str:tokenize($articolo/data_ora_inizio_pubblicazione, ' ')[1]"/>
		alle: <xsl:value-of select="str:tokenize($articolo/data_ora_inizio_pubblicazione, ' ')[2]"/>
		]
		[ Commenti: 
		<a href='articolo.php?id_articolo={$articolo/id_articolo}#commenti'>
			<xsl:value-of select="$articolo/nr_commenti"/>
		</a>
		] 
		<br />
		[ Categoria: 
		<a href='index.php?id_categoria_articolo={$articolo/id_categoria_articolo}'>
			<xsl:value-of select="$articolo/nome_categoria"/>
		</a>
		] 
		<xsl:if test="$articolo/tags != ''">
			[ Tags: 
			<xsl:for-each select="str:tokenize($articolo/tags,',')">
				<a href='index.php?tag={normalize-space(.)}'>
					<xsl:value-of select="normalize-space(.)"/>
				</a>
				<xsl:text>&#10;</xsl:text>
			</xsl:for-each>
			] 
		</xsl:if>
	</div>

</xsl:template>
	
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="blocco_argomento">
	<xsl:param name="argomento"/>

	<h1 class="argomento_titolo">
		<a href='argomento.php?id_argomento={$argomento/id_argomento}'>
			<xsl:value-of disable-output-escaping="yes" select="$argomento/titolo"/>
		</a>
	</h1>

	<xsl:if test="$argomento/abstract != ''">
		<div class="argomento_sintesi">
			<xsl:value-of disable-output-escaping="yes" select="$argomento/abstract"/>
		</div>
	</xsl:if>

	<xsl:call-template name="dati_argomento">
		<xsl:with-param name="argomento" select="$argomento" />
	</xsl:call-template>

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="dati_argomento">
	<xsl:param name="argomento"/>

	<div class="datiArgomento">
		[ Aperto da: 
		<a href='vedi_utente.php?id_utente={$argomento/id_utente}'>
			<xsl:value-of select="$argomento/nickname"/>
		</a>
		il: <xsl:value-of select="str:tokenize($argomento/data_ora_inizio_pubblicazione, ' ')[1]"/>
		alle: <xsl:value-of select="str:tokenize($argomento/data_ora_inizio_pubblicazione, ' ')[2]"/>
		]
		[ Interventi: 
		<a href='argomento.php?id_argomento={$argomento/id_argomento}#interventi'>
			<xsl:value-of select="$argomento/nr_interventi"/>
		</a>
		] 
		<br />
		[ Categoria: 
		<a href='forum.php?id_categoria_argomento={$argomento/id_categoria_argomento}'>
			<xsl:value-of select="$argomento/nome_categoria"/>
		</a>
		] 
		<xsl:if test="$argomento/tags != ''">
			[ Tags: 
			<xsl:for-each select="str:tokenize($argomento/tags,',')">
				<a href='forum.php?tag={normalize-space(.)}'>
					<xsl:value-of select="normalize-space(.)"/>
				</a>
				<xsl:text>&#10;</xsl:text>
			</xsl:for-each>
			] 
		</xsl:if>
	</div>

</xsl:template>
	
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="messaggio_errore">

	<xsl:param name="messaggio" />

	<xsl:if test="$messaggio">
		<div class="messaggio errore">
			<xsl:value-of disable-output-escaping="yes" select="$messaggio" />
		</div>
	</xsl:if>

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="messaggio_ok">

	<xsl:param name="messaggio" />

	<xsl:if test="$messaggio">
		<div class="messaggio conferma">
			<xsl:value-of disable-output-escaping="yes" select="$messaggio" />
		</div>
	</xsl:if>

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="modulo_login">
	<xsl:param name="descr_azione" />

	<!-- Login -->
	<div style="width: 1px; color:transparent; font-size: 1px;">a</div>
	<h1>Login</h1>
	<div class="articolo_blocco">
		<p>
			<xsl:value-of select="$descr_azione" /> devi effettuare il login.
			Se non sei ancora registrato <a href="registrazione.php">vai alla pagina di registrazione</a>.
		</p>
		<form id="wamodulo" method="post" onsubmit="return check_email(this)">
			<input type="hidden" id="wamodulo_nome_modulo" name="wamodulo_nome_modulo" value="wamodulo" />
			<input type="hidden" name="wamodulo_operazione" value="3" />
			<input type="hidden" name="pagina_redirect" value="{/waapplicazione/pagina/uri}" />
			<label>Login *</label> (indirizzo email usato per la registrazione)
			<div class="campo">
				<input type="text" name="email" value="{/waapplicazione/pagina/elementi/elemento[nome='email']/valore}" />
			</div>
			<label>Password</label>
			<div class="campo">
				<input type="password" name="pwd" />
			</div>	
			<div class="campo">
				<input type="submit" value="Login"/>
			</div>	
		</form>
		(se hai dimenticato la password, inserisci solo l'indirizzo email e lascia il campo 
		<strong>Password</strong> vuoto; la password ti sara'
		inviata all'indirizzo email indicato)
	</div>

</xsl:template>

<!-- *********************************************************************** -->
<!-- ***** toglie un parametro dalla uri *********************************** -->
<!-- *********************************************************************** -->
<xsl:template name="rimuovi_da_uri">
	<xsl:param name="uri" />
	<xsl:param name="parametro" />

	<xsl:choose>
		<xsl:when test="contains($uri, concat('?', $parametro))">
			<xsl:value-of select="substring-before($uri, concat('?', $parametro))" />
			<xsl:if test="string-length(substring-after($uri, concat('?', $parametro))) &gt; 0">
				<xsl:text>?</xsl:text>
			</xsl:if>
			<xsl:value-of select="substring-after($uri, concat('?', $parametro))" />
		</xsl:when>
		<xsl:when test="contains($uri, concat('&amp;', $parametro))">
			<xsl:value-of select="substring-before($uri, concat('&amp;', $parametro))" />
			<xsl:if test="string-length(substring-after($uri, concat('&amp;', $parametro))) &gt; 0">
				<xsl:value-of select="substring-after($uri, concat('&amp;', $parametro))" />
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$uri" />
		</xsl:otherwise>
	</xsl:choose>	

</xsl:template>

<!-- *********************************************************************** -->
<!-- ***** bottoni di paginazione di un recordset ************************** -->
<!-- *********************************************************************** -->
<xsl:template name="bottoni_paginazione">
	<xsl:param name="rs" />
	<xsl:param name="anchor" />

	<div class="bottoni_paginazione">
		<xsl:variable name="uri_senza_nr_pag">
			<xsl:call-template name="rimuovi_da_uri">
				<xsl:with-param name="uri" select="/waapplicazione/pagina/uri"/>
				<xsl:with-param name="parametro" select="concat('pag_', $rs/../nome, '=', $rs/nr_pagina)"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="qoe">
			<xsl:choose>
				<xsl:when test="contains($uri_senza_nr_pag, '?')">&amp;</xsl:when>
				<xsl:otherwise>?</xsl:otherwise>
			</xsl:choose>	
		</xsl:variable>

		<xsl:if test="$rs/nr_pagina > 0">
			<div class="pagina_precedente">
				<a href="{$uri_senza_nr_pag}{$qoe}pag_{$rs/../nome}={$rs/nr_pagina - 1}#{$anchor}">
					&lt;&lt; Precedenti
				</a>
				<br/>
				<a href="{$uri_senza_nr_pag}{$qoe}pag_{$rs/../nome}={0}#{$anchor}">
					&lt;&lt;&lt;&lt; Prima pagina
				</a>
			</div>
		</xsl:if>

		<xsl:variable name="tot_pagine">
			<xsl:variable name="sfrido">
				<xsl:choose>
					<xsl:when test="$rs/nr_righe_senza_limite mod $rs/righe_x_pagina = 0">0</xsl:when>
					<xsl:otherwise>1</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:value-of select="floor($rs/nr_righe_senza_limite div $rs/righe_x_pagina) + $sfrido" />
		</xsl:variable>
		<xsl:if test="$rs/nr_pagina &lt; ($tot_pagine - 1)">
			<div class="pagina_successiva">
				<a href="{$uri_senza_nr_pag}{$qoe}pag_{$rs/../nome}={$rs/nr_pagina + 1}#{$anchor}">
					Successivi &gt;&gt;
				</a>
				<br/>
				<a href="{$uri_senza_nr_pag}{$qoe}pag_{$rs/../nome}={$tot_pagine - 1}#{$anchor}">
					Ultima pagina &gt;&gt;&gt;&gt;
				</a>
			</div>
		</xsl:if>
	</div>

</xsl:template>

</xsl:stylesheet>