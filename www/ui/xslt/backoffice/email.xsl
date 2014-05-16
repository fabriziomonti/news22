<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template email                                                         -->
<!-- ********************************************************************** -->
<xsl:template match="email">

	<xsl:call-template name="testo"/>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="email.input">

	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">
				<xsl:value-of select="/wamodulo.input/post/item[@id=$id]" />
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>


</xsl:stylesheet>