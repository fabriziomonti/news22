<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template selezione_ext                                                 -->
<!-- ********************************************************************** -->
<xsl:template match="selezione_ext">

	<xsl:call-template name="intestazione_controllo"/>
	<input type='hidden' name='{@id}' value='{valore}' />
	<input type='text' name='{@id}_testo' value='{testo}' autocomplete='off'>
		<xsl:call-template name="dammiattributicontrollo">
		</xsl:call-template>
	</input>
	<xsl:variable name='alto_lista' select='alto + 22' />
	<div id='waselezione_ext_lista_{@id}' class='wamodulo_selezione_ext_lista' style='visibility: hidden; top: {$alto_lista}px; left: {sinistra}px; width: {larghezza}px;'>
	</div>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="selezione_ext.input">

	<xsl:variable name="id" select="@id" />
	<xsl:element name="{@id}">
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">
				<xsl:element name="valore">
					<xsl:value-of select="/wamodulo.input/post/item[@id=$id]" />
				</xsl:element>
				<xsl:element name="testo">
					<xsl:value-of select="/wamodulo.input/post/item[@id = concat($id, '_testo')]" />
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="valore">
					__wamodulo_valore_non_ritornato__
				</xsl:element>
				<xsl:element name="testo" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>