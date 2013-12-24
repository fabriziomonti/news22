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
	<xsl:variable name="articolo" select="exsl:node-set(pagina/elementi/elemento[nome='articolo']/valore/riga)" />

	<html>
		<xsl:call-template name="pagina_head_html" >
			<xsl:with-param name="id_articolo" select="$articolo/id_articolo" />
		</xsl:call-template>
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />

					<!-- COLONNA SX -->
					<div id="colonnaSx">

						<!--facciamo vedere il contenuto della pagina solo se non  abbiamo ricevuito un errore-->
						<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']/valore)">

							<div class="articolo_blocco">
								<xsl:call-template name="blocco_articolo">
									<xsl:with-param name="articolo" select="$articolo" />
									<xsl:with-param name="tipo_posizione" select="'articolo'" />
									<xsl:with-param name="flag_testo" select="1" />
								</xsl:call-template>
							<a name="commenti" />
							</div><!-- id="articolo_blocco" -->

							<!--bottoni di paginazione-->
							<xsl:call-template name="bottoni_paginazione" >
								<xsl:with-param name="rs" select="pagina/elementi/elemento[nome='commenti']/valore" />
								<xsl:with-param name="anchor" select="'commenti'" />
							</xsl:call-template>

							<!--blocco dei commenti-->
							<xsl:for-each select="pagina/elementi/elemento[nome='commenti']/valore/riga" >
								<xsl:variable name="livello">
									<xsl:value-of select="(string-length(chiave_ordinamento) - string-length(translate(chiave_ordinamento, '_', '')))" />
								</xsl:variable>

								<div class="elencoCommenti" style="width: {99 - $livello * 2}%; margin-left: {$livello * 2}%">
									<a name="commento_{id_commento}" />
									<xsl:if test="avatar != ''">
										<img src="../downloaddoc.php?tabella=utenti&amp;tipo=avatar&amp;id={id_utente}" />
									</xsl:if>

									<div id="commento_{id_commento}">
										<xsl:value-of disable-output-escaping="yes" select="testo"/>
									</div>
									<p />

									[ Pubblicato da: 
									<a href='vedi_utente.php?id_utente={id_utente}' id='nickname_{id_commento}'>
										<xsl:value-of select="nickname"/>
									</a>
									il: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
									alle: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
									]

									<!--302 = privilegio inserimento commenti tutti; 303 inserimento propri-->
									<xsl:if test="$dati_utente/supervisore = 1 or
													$dati_utente/privilegi/elemento_id_302 = 302 or 
													$dati_utente/privilegi/elemento_id_303 = 303">
										<a href="javascript:rispondiCommento('{id_articolo}', '{id_commento}')">
											<img src="../ui/img/street/Rispondi.gif" style="float: right; margin-right: 8px;" alt="Rispondi" title="Rispondi" />
										</a>
									</xsl:if>

									<!--abilitazione bottoni modifica/cancella per chi ha i privilegi-->
									<!--304 = privilegio modifica tutti; 305 modifica propri-->
									<xsl:if test="$dati_utente/supervisore = 1 or
													$dati_utente/privilegi/elemento_id_304 = 304 or 
													($dati_utente/privilegi/elemento_id_305 = 305 and $dati_utente/id_utente = id_utente)">
										<a href="javascript:modificaCommento('{id_articolo}', '{id_commento}')">
											<img src="../ui/img/street/Modifica.gif" style="float: right; margin-right: 8px;" alt="Modifica" title="Modifica" />
										</a>
									</xsl:if>

									<!--306 = privilegio elimina tutti; 307 elimina propri-->
									<xsl:if test="tengo_famiglia = 0 and
													($dati_utente/supervisore = 1 or
													$dati_utente/privilegi/elemento_id_306 = 306 or 
													($dati_utente/privilegi/elemento_id_307 = 307 and $dati_utente/id_utente = id_utente)
													)">
										<a href="javascript:eliminaCommento('{id_articolo}', '{id_commento}')">
											<img src="../ui/img/street/Elimina.gif" style="float: right; margin-right: 8px;" alt="Elimina" title="Elimina" />
										</a>										
									</xsl:if>
								</div>

							</xsl:for-each>

							<!--bottoni di paginazione-->
							<xsl:call-template name="bottoni_paginazione" >
								<xsl:with-param name="rs" select="pagina/elementi/elemento[nome='commenti']/valore" />
								<xsl:with-param name="anchor" select="'commenti'" />
							</xsl:call-template>

							<xsl:if test="not($dati_utente)">
								<xsl:call-template name="modulo_login">
									<xsl:with-param name="descr_azione">
										Per poter lasciare un commento 
									</xsl:with-param>
								</xsl:call-template>
							</xsl:if>

							<xsl:if test="$dati_utente">
								<div style="width: 1px; color:transparent; font-size: 1px;">a</div>
								<h1 id="label_testo_commento">Inserisci un commento</h1>
								<xsl:if test="$dati_utente/privilegio_html_base = 1 and $dati_utente/privilegio_html_esteso = 0">	
									(sono ammessi i tag HTML &lt;b&gt; &lt;u&gt; &lt;i&gt;)
								</xsl:if>

								<div class="notizia_blocco">
									<!-- form commento -->
									<form id="wamodulo" method="post" onsubmit="return validaForm(this)">
										<input type="hidden" id="wamodulo_nome_modulo" name="wamodulo_nome_modulo" value="wamodulo" />
										<input type="hidden" name="wamodulo_operazione" value="3" />
										<div class="campo">
											<textarea name='testo' class="mceEditor"></textarea>
										</div>
										<div class="campo">
											<input type='submit' name="cmd_invia" value='invia' />
											<input type='button' name="annulla_modifica" value='annulla' onclick="annullaModificaCommento('{pagina/elementi/elemento[nome='articolo']/valore/riga/id_articolo}')" style="visibility:hidden" />
										</div>
									</form>
								</div>

							</xsl:if> <!--dati utente-->
							
						</xsl:if> <!--no messaggio errore -->




					</div><!-- id="colonnaSx" -->
					
					<xsl:call-template name="pagina_footer" />
	
				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->
		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>