<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="watabella_template_comuni.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

<!-- ********************************************************************** -->
<!--  template della tabella -->
<!-- ********************************************************************** -->
<xsl:template match="watabella">

	<xsl:text>&#10;</xsl:text>
	<link href='{watabella_path}/uis/wa_file_comuni/css/watabella.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
	<link href='{watabella_path}/../../ui/css/backoffice/tabella.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
	
	<!-- roba menu contestuale	(una parte...) -->
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/strmanage.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/watabella.js'></script><xsl:text>&#10;</xsl:text>

 	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	<xsl:call-template name="watabella_finestra_ordinamento_filtro" />
	
	<form action='{uri}' id='{nome}_bottoniera' class='watabella' onsubmit='return document.{nome}.ricercaRapida()'><xsl:text>&#10;</xsl:text>
		<div>
			<xsl:apply-templates select="watabella_azioni_pagina"/>
			<xsl:apply-templates select="watabella_ricerca_rapida"/>
		</div>
	</form><xsl:text>&#10;</xsl:text>
	<xsl:apply-templates select="watabella_barra_navigazione"/>
	<form id='{nome}' action='' method='post' class='watabella'>
	    <table style='width: 100%'>
			<xsl:apply-templates select="watabella_intestazioni"/>
			<xsl:apply-templates select="watabella_riga_totali"/>
			<xsl:apply-templates select="watabella_righe"/>
	    </table>
	</form>
	
	<!-- creazione degli oggetti javascript -->
	<xsl:call-template name="crea_oggetti_javascript"/>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template intestazioni colonne -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_intestazioni">
 	
	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<thead>
		<tr id='{/watabella/nome}_intestazioni'>
		 	<th></th>
			<xsl:for-each select="intestazione[mostra=1]">
			 	<xsl:variable name="alignment">
					<xsl:choose>
						<xsl:when test="allineamento = 1">center</xsl:when>
						<xsl:when test="allineamento = 2">right</xsl:when>
						<!--default x tipo campo-->
						<xsl:when test="tipo_campo = 'INTERO'">right</xsl:when>
						<xsl:when test="tipo_campo = 'DECIMALE'">right</xsl:when>
						<xsl:when test="tipo_campo = 'DATA'">center</xsl:when>
						<xsl:when test="tipo_campo = 'DATAORA'">center</xsl:when>
						<xsl:when test="tipo_campo = 'ORA'">center</xsl:when>
						<xsl:otherwise>left</xsl:otherwise>
					</xsl:choose>	
			 	</xsl:variable>
				<th style='text-align: {$alignment}' id='{/watabella/nome}_{nome}'>
					<xsl:choose>
						<xsl:when test="ordina = '1'">
						 	<xsl:variable name="modo_ordinamento">
								<xsl:if test="ordinamento_rapido = 'asc'">desc</xsl:if>
								<xsl:if test="ordinamento_rapido = 'desc' or ordinamento_rapido = 'no'">asc</xsl:if>
						 	</xsl:variable>
							<a href='{/watabella/uri}{$qoe}watbl_or[{/watabella/nome}]={nome}&amp;watbl_orm[{/watabella/nome}]={$modo_ordinamento}'>
								<xsl:value-of select="etichetta"/>
								<xsl:if test="ordinamento_rapido != 'no'">
									<center>
										<img src='{/watabella/watabella_path}/uis/wa_file_comuni/img/{ordinamento_rapido}_order.gif' border='0'/>
									</center>
								</xsl:if>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="etichetta"/>
						</xsl:otherwise>
					</xsl:choose>	
				</th>
			</xsl:for-each>
		</tr>
	</thead>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle righe -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_righe">
	
	<tbody>
		<xsl:for-each select="riga">
			<xsl:variable name="objRiga">document.<xsl:value-of select="/watabella/nome"/>.righe['<xsl:value-of select="@id"/>']</xsl:variable>
			<tr id='row_{/watabella/nome}_{@id}' onclick='{$objRiga}.cambiaStato()'>
			
				<!-- valorizzazione azioni in linea -->
				<xsl:call-template name="azioni_record" />
			
				<xsl:text>&#10;</xsl:text>
				<xsl:for-each select="cella">
					<xsl:variable name="cellpos" select="position()" />
					<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
				 	<xsl:if test="$col_info/mostra = 1">
					 	<xsl:variable name="alignment">
							<xsl:choose>
								<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
								<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
								<!--default x tipo campo-->
								<xsl:when test="$col_info/tipo_campo = 'INTERO'">right</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">right</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATA'">center</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATAORA'">center</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'ORA'">center</xsl:when>
								<xsl:otherwise>left</xsl:otherwise>
							</xsl:choose>	
					 	</xsl:variable>
						<td style='text-align: {$alignment}'>
							<xsl:choose>
								<xsl:when test="$col_info/link = 1">
									<a href='javascript:document.{/watabella/nome}.link_{/watabella/nome}_{$col_info/nome}("{../@id}")'>
										<xsl:value-of select="valore"/>
									</a>
								</xsl:when>
								<xsl:when test="$col_info/converti_html = '0'">
									<!--maialata per gli emoticons...-->
									<xsl:call-template name="risolvi_emoticon">
										<xsl:with-param name="string" select="valore"/>
									</xsl:call-template>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATAORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
											<xsl:text>&#x20;</xsl:text>
										<xsl:value-of select="substring(valore, 12, 2)"/>:<xsl:value-of select="substring(valore, 15, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'ORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 1, 2)"/>:<xsl:value-of select="substring(valore, 4, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="linebreak">
										<xsl:with-param name="text" select="valore"/>
									</xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</xsl:if>
				</xsl:for-each>
				
				
			</tr>
		</xsl:for-each>
	</tbody>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template riga dei totali -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_riga_totali">
	<tfoot>
		<tr>
			<th></th>
			<xsl:for-each select="cella">
				<xsl:variable name="cellpos" select="position()" />
				<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
				<xsl:if test="$col_info/mostra = 1">
					<xsl:variable name="alignment">
						<xsl:choose>
							<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
							<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
							<xsl:otherwise>left</xsl:otherwise>
						</xsl:choose>	
					</xsl:variable>
					<th style='text-align: {$alignment}'>
						<xsl:choose>
							<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
								<xsl:if test="string-length(valore) &gt; 0">
									<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="valore"/>
							</xsl:otherwise>
						</xsl:choose>
					</th>
				</xsl:if>
			</xsl:for-each>
		</tr>
	</tfoot>
</xsl:template>


<!-- ********************************************************************** -->
<!-- template del controllo per la ricerca rapida -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_ricerca_rapida">
	<!--<br/>-->
	<div style="float: right">
		<input name='watbl_rr[{/watabella/nome}]' value='{valore}'/><span><button type='submit' title='Cerca' value='Cerca'>Cerca</button></span>
	</div>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="azioni_record">
	<xsl:variable name="record" select="."/>

	<xsl:text>&#10;</xsl:text>
	<td style='width: 1%'>
 		<xsl:variable name="id_riga" select="@id"/>
		<xsl:for-each select="/watabella/watabella_azioni_record/azione">
			<!-- verifichiamo se il bottone e' abilitato -->
			<xsl:variable name="id_azione" select="@id"/>
			<xsl:if test="not($record/azioni_abilitabili/azione[@id=$id_azione]) or $record/azioni_abilitabili/azione[@id=$id_azione]/abilitazione = '1'">
				<button type='button' onclick='document.{/watabella/nome}.azione_{/watabella/nome}_{nome}("{$id_riga}")'>
					<xsl:value-of select="etichetta"/>
				</button>
			</xsl:if>
		</xsl:for-each>
	</td>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
</xsl:stylesheet>
