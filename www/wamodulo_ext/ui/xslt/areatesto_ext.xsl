<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template areatesto                                                     -->
<!-- ********************************************************************** -->
<xsl:template match="areatesto_ext">

	<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/tinymce/4.0.21/tinymce.min.js'></script><xsl:text>&#10;</xsl:text>
	<xsl:call-template name="intestazione_controllo"/>
	
	<div class="wamodulo_areatesto_ext" style="position: absolute; top: {alto}px; left: {sinistra}px;">
	<!--<div class="wamodulo_areatesto_ext" >-->
		<textarea name='{@id}' id='{@id}'>
			<xsl:call-template name="dammiattributicontrollo"/>
			<xsl:attribute name="class">mceEditor</xsl:attribute>
			<xsl:value-of select="valore"/>		
		</textarea>
	</div>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="areatesto_ext.input">
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