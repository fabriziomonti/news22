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

	<!-- node-set contenente gli articoli-->
	<xsl:variable name="articoli" select="exsl:node-set(pagina/elementi/elemento[nome='articoli']/valore)" />

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />
					

					<!-- se non è stata specificata una restrizione alla ricerca, 
					allora siamo in home: non diamo risalto all'ultimo articolo-->
					<xsl:variable name="primo_piano" select="not(substring-after(pagina/uri, '?'))" />

					<xsl:if test="$primo_piano">
						<!-- IN PRIMO PIANO -->
						<div class="inPrimoPiano_blocco">
							<xsl:call-template name="blocco_articolo">
								<xsl:with-param name="articolo" select="exsl:node-set($articoli/riga[position() = 1])" />
								<xsl:with-param name="tipo_posizione" select="'inPrimoPiano'" />
								<xsl:with-param name="flag_testo" select="1" />
							</xsl:call-template>
						</div><!-- id="inPrimoPiano_blocco" -->
					</xsl:if>
					
					<!-- COLONNA SX -->
					<xsl:variable name="primo_articolo" select="number($primo_piano)" />
					<div id="colonnaSx">
						<xsl:for-each select="$articoli/riga[position() > $primo_articolo]">
							<div class="articolo_blocco">
								<xsl:call-template name="blocco_articolo">
									<xsl:with-param name="articolo" select="exsl:node-set(.)" />
									<xsl:with-param name="tipo_posizione" select="'articolo'" />
								</xsl:call-template>
							</div><!-- id="articolo_blocco" -->
						</xsl:for-each>

						<!--bottoni di paginazione-->
						<xsl:call-template name="bottoni_paginazione" >
							<xsl:with-param name="rs" select="$articoli" />
						</xsl:call-template>


					</div><!-- id="colonnaSx" -->
					
					<xsl:call-template name="pagina_footer" />

				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->
		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>