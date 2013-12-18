<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" 
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				xmlns:str="http://exslt.org/strings" exclude-result-prefixes="str"
				xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl"
>
	<xsl:output method="html" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes"/>


	<!-- *********************************************************************** -->
	<!--  template della pagina -->
	<!-- *********************************************************************** -->
	<xsl:template match="waapplicazione">

		<html>
			<body>

				<xsl:apply-templates select="pagina/elementi/elemento"/>

			</body>
		</html>

	</xsl:template>

	<!-- ********************************************************************** -->
	<!-- template messaggio -->
	<!-- ********************************************************************** -->
	<xsl:template match="pagina/elementi/elemento">

		<xsl:if test="nome = 'messaggio'">
			<script type='text/javascript'>
				alert("<xsl:value-of select="valore" />");
				if (window.opener) 
					self.close();
				else
				   location.href = "index.php";
			</script>
			<xsl:text>&#10;</xsl:text>
		</xsl:if>

	</xsl:template>

	
</xsl:stylesheet>