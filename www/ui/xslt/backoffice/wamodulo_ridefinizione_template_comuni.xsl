<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ************************   SUBROUTINES   ***************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

<!-- ********************************************************************** -->
<!-- subroutine template dammilayout -->
<!-- ********************************************************************** -->
<xsl:template name="dammilayout">
	<xsl:param name="offset_sinistra" />
	<xsl:param name="allineamento_testo" />
	<xsl:param name="src_parametri" select="."/>
	
	<xsl:attribute name='style'>
		<xsl:if test="$src_parametri/visibile = '0'">visibility:hidden;</xsl:if>
		<xsl:if test="$src_parametri/larghezza > 200">width: 98%;</xsl:if>
		<xsl:if test="$src_parametri/altezza != '' and $src_parametri/altezza != '0' ">height:<xsl:value-of select="$src_parametri/altezza"/>px;</xsl:if>
		<xsl:if test="$offset_sinistra != ''">
			<xsl:text>left:</xsl:text><xsl:value-of select="$offset_sinistra"/><xsl:text>px;</xsl:text>
		</xsl:if>
		<xsl:if test="$allineamento_testo != ''">text-align:<xsl:value-of select="$allineamento_testo"/>;</xsl:if>
	</xsl:attribute>
		
</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template creazione bottoni calendario per le date          -->
<!-- ********************************************************************** -->
<xsl:template name="bottone_calendario">
	<xsl:param name="tipo" />
	<xsl:param name="offset_sinistra"/>

	<xsl:text>&#10;</xsl:text>
	<input name='wamodulo_{$tipo}cal_{@id}' title='Calendario {$tipo}' type='button'>
		<xsl:if test="indice_tab != '' and indice_tab != '0' "><xsl:attribute name='tabindex'><xsl:value-of select="indice_tab"/></xsl:attribute></xsl:if>
		<xsl:if test="sola_lettura = '1'"><xsl:attribute name='disabled'>disabled</xsl:attribute></xsl:if>
		<xsl:attribute name='style'>
			<xsl:if test="visibile = '0'">visibility:hidden;</xsl:if>
			<xsl:text>width:30px;</xsl:text>
			<xsl:text>left:</xsl:text><xsl:value-of select="$offset_sinistra"/><xsl:text>px;</xsl:text>
			<xsl:if test="$tipo = 'anno'">margin-right: 4em;</xsl:if>
		</xsl:attribute>
		<xsl:attribute name='value'>
			<xsl:text>...</xsl:text><xsl:if test="$tipo = 'anno'">.</xsl:if>
		</xsl:attribute>
		
		<xsl:attribute name='onclick'>
			<xsl:text>myShow</xsl:text>
			<xsl:if test="$tipo = 'mese'">Month</xsl:if>
			<xsl:if test="$tipo = 'anno'">Year</xsl:if>
			<xsl:text>Cal(this.form.wamodulo.nome + ".obj.pass_</xsl:text>
			<xsl:value-of select="@id"/>
			<xsl:text>")</xsl:text>
		</xsl:attribute>
		
	</input>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>