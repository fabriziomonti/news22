<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" 
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				xmlns:str="http://exslt.org/strings" exclude-result-prefixes="str"
				xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl"
>
<xsl:import href="template_base.xsl"/>
<xsl:output method="html" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes"/>


<!-- *********************************************************************** -->
<!--  template della pagina -->
<!-- *********************************************************************** -->
<xsl:template match="waapplicazione">

	<xsl:variable name="dati_utente" select="exsl:node-set(pagina/elementi/elemento[nome='dati_utente']/valore)" />

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />
					
					<!--se l'utente è loggato diamo il messagio di benvenuto-->
					<xsl:if test="$dati_utente">
						<xsl:call-template name="messaggio_ok" >
							<xsl:with-param name="messaggio">
								Ciao <xsl:value-of select="$dati_utente/nickname"/>, ora sei loggato.
							</xsl:with-param>
						</xsl:call-template>
					</xsl:if>
					
					<!-- COLONNA SX -->
					<div id="corpoCentrale">
						
						<!-- se l'utente non è loggato mostriamo la form di login -->
						<xsl:if test="not($dati_utente)">
							<xsl:call-template name="modulo_login">
								<xsl:with-param name="descr_azione">
									Per poter lasciare un commento al blog o un intervento nel forum
								</xsl:with-param>
							</xsl:call-template>
						</xsl:if>
					
					</div><!-- id="corpoCentrale" -->
					
					<xsl:call-template name="pagina_footer" />
	
				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->

		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>