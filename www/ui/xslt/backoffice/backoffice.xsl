<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

<!-- ********************************************************************** -->
<!--  ************* template della pagina ********************************* -->
<!-- ********************************************************************** -->
<xsl:template match="waapplicazione">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<link href='{waapplicazione_path}/uis/wa_default/css/waapplicazione.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{waapplicazione_path}/uis/wa_default/js/strmanage.js'></script><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{waapplicazione_path}/uis/wa_default/js/moo1.2.js'></script><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{waapplicazione_path}/uis/wa_default/js/waapplicazione.js'></script><xsl:text>&#10;</xsl:text>
			
		    <title>
		    	<xsl:value-of select="titolo" />
		    </title>
		</head>
		<body onunload='document.wapagina.chiudiFiglia()'>
			<noscript>
				<hr />
				<div style='text-align: center'>
					<b>
						Questa applicazione usa Javascript, ma il tuo browser ha questa funzione
						disabilitata. Sei pregato di abilitare Javascript per il dominio <xsl:value-of select="dominio" />
						e ricaricare la pagina.
					</b>
				</div>
				<hr />
			</noscript>
			
			<!-- se lavoriamo con navigazione interna creiamo anche l'iframe destinato a contenere la finestra figlia-->
			<xsl:text>&#10;</xsl:text>
			<xsl:if test="modalita_navigazione = '3'">
				<iframe id='waapplicazione_iframe_figlia' class='waapplicazione_iframe_figlia' style='visibility:hidden'>
				</iframe>
			</xsl:if>
	
			<!-- creazione degli elementi costitutivi della pagina (titolo, tabelle, moduli, testo libero, ecc.-->
			<xsl:apply-templates select="pagina/elementi/elemento"/>

			<!-- tentativi euristici: qui l'xsl tenta sempre di caricare:-->
			<!-- - un css dell'applicazione (directory_di_lavoro/ui/css/nome_applicazione.css)-->
			<!-- - un css della sezione (directory_di_lavoro/ui/css/{sigla_sezione}/{sigla_sezione}.css)-->
			<!-- - un css della pagina  (directory_di_lavoro/ui/css/nome_pagina.css)-->
			<!-- - un js dell'applicazione (directory_di_lavoro/ui/js/nome_applicazione.js)-->
			<!-- - un js della sezione (directory_di_lavoro/ui/js/{sigla_sezione}/{sigla_sezione}.js)-->
			<!-- - un js della pagina  (directory_di_lavoro/ui/js/nome_pagina.js)-->
			<!-- i js della pagina sono sempre gli ultimi a dover essere caricati, altrimenti non vedono le strutture altrui... -->
			<link href='{directory_lavoro}/ui/css/{nome}.css' rel='stylesheet'/>
			<xsl:text>&#10;</xsl:text>
			<link href='{directory_lavoro}/ui/css/{sigla_sezione}/{sigla_sezione}.css' rel='stylesheet'/>
			<xsl:text>&#10;</xsl:text>
			<link href='{directory_lavoro}/ui/css/{sigla_sezione}/{pagina/nome}.css' rel='stylesheet'/>
			<xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{directory_lavoro}/ui/js/{nome}.js'></script>
			<xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{directory_lavoro}/ui/js/{sigla_sezione}/{sigla_sezione}.js'></script>
			<xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{directory_lavoro}/ui/js/{sigla_sezione}/{pagina/nome}.js'></script>
			<xsl:text>&#10;</xsl:text>

			<!-- se non esiste il file js relativo alla pagina, creiamo un oggetto pagina che ha le proprieta' -->
			<!-- e i metodi di default dell'applicazione. -->
			<!-- In ogni caso diciamo all'applicazione/pagina in che modalita' si dovra' navigare -->
			<!-- e se la pagina deve allineare la mamma e/o eventualmente chiudersi -->
			<script type='text/javascript'>
				if (!document.wapagina)
					document.wapagina = new backoffice();
				document.wapagina.modalitaNavigazione = '<xsl:value-of select="modalita_navigazione" />';
				<xsl:if test="pagina/ritorno/valori">
					document.wapagina.allineaGenitore('<xsl:value-of select="pagina/ritorno/valori" />');
					<xsl:if test="pagina/ritorno/chiudi">
						document.wapagina.chiudiPagina();
					</xsl:if>
				</xsl:if>

			</script><xsl:text>&#10;</xsl:text>
			
		</body>
	</html>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template dei menu -->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'wamenu']">
	<xsl:text>&#10;</xsl:text>
	<div class="waapplicazione_{nome}">
		<xsl:value-of disable-output-escaping="yes" select="valore" />
	</div>
	<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle tabelle -->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'watabella']">
	<xsl:text>&#10;</xsl:text>
	<div class="waapplicazione_{nome}">
		<xsl:value-of disable-output-escaping="yes" select="valore" />
	</div>
	<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template moduli-->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'wamodulo']">
	<xsl:text>&#10;</xsl:text>
	<div class="waapplicazione_{nome}">
		<xsl:value-of disable-output-escaping="yes" select="valore" />
	</div>
	<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template boh -->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'stringa']">
</xsl:template>

<!-- ********************************************************************** -->
<!-- template essaggio -->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'stringa' and nome = 'messaggio']">


	<div class="waapplicazione_messaggio_errore">
		<xsl:value-of select="valore" />
		<form>
			<input type="button" onclick="chiudi()" value="Chiudi" />
		</form>
	</div>
	<script type='text/javascript'>
		function chiudi()
			{
			if (window.opener) 
				self.close();
			else
			   history.back();
			}
	</script>
	<xsl:text>&#10;</xsl:text>

</xsl:template>

<!-- ********************************************************************** -->
<!-- template titolo pagina -->
<!-- ********************************************************************** -->
<xsl:template match="pagina/elementi/elemento[tipo = 'stringa' and nome = 'titolo']">
	<xsl:text>&#10;</xsl:text>
	<div class="waapplicazione_{nome}">
		<xsl:variable name="sottotitolo" select="substring-after(valore, '&#xa;')" />
		<xsl:choose>

			<xsl:when test="string-length($sottotitolo) &gt; 0">
				<xsl:variable name="titolo" select="substring-before(valore, '&#xa;')" />	
				<xsl:value-of disable-output-escaping="yes" select="$titolo" />
				<br />
				<b>
					<xsl:value-of disable-output-escaping="yes" select="$sottotitolo" />
				</b>
			</xsl:when>

			<xsl:otherwise>
				<xsl:value-of disable-output-escaping="yes" select="valore" />
			</xsl:otherwise>

		</xsl:choose>

	</div>
	<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
</xsl:stylesheet>