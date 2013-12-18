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

	<!-- node-set contenente gli argomenti-->
	<xsl:variable name="argomenti" select="exsl:node-set(pagina/elementi/elemento[nome='argomenti']/valore)" />

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />
					

					<!-- COLONNA SX -->
					<div id="colonnaSx">
						<xsl:for-each select="$argomenti/riga">
							<div class="argomento_blocco">
								<xsl:call-template name="blocco_argomento">
									<xsl:with-param name="argomento" select="exsl:node-set(.)" />
								</xsl:call-template>
							</div><!-- id="argomento_blocco" -->
						</xsl:for-each>

						<!--bottoni di paginazione-->
						<xsl:call-template name="bottoni_paginazione" >
							<xsl:with-param name="rs" select="$argomenti" />
						</xsl:call-template>


					</div><!-- id="colonnaSx" -->
					
					<xsl:call-template name="pagina_footer" />

				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->
		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>