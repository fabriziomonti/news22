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
	<xsl:variable name="argomento" select="exsl:node-set(pagina/elementi/elemento[nome='argomento']/valore/riga)" />

	<html>
		<xsl:call-template name="pagina_head_html" >
			<xsl:with-param name="id_argomento" select="$argomento/id_argomento" />
		</xsl:call-template>
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />

					<!-- COLONNA SX -->
					<div id="corpoCentrale">

						<!--facciamo vedere il contenuto della pagina solo se non  abbiamo ricevuito un errore-->
						<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']/valore)">

							<div class="argomento_blocco">
								<xsl:call-template name="blocco_argomento">
									<xsl:with-param name="argomento" select="$argomento" />
								</xsl:call-template>
							</div><!-- id="argomento_blocco" -->

							<div class="elencoInterventi" >
								<a name="interventi"></a>
								
								<!--bottoni feeder-->
								<xsl:call-template name="blocco_feeder" />
								
								<!--bottoni di paginazione-->
								<xsl:call-template name="bottoni_paginazione" >
									<xsl:with-param name="rs" select="pagina/elementi/elemento[nome='interventi']/valore" />
									<xsl:with-param name="anchor" select="'interventi'" />
								</xsl:call-template>

								<!--blocco degli interventi-->
								<xsl:for-each select="pagina/elementi/elemento[nome='interventi']/valore/riga" >
									<xsl:variable name="livello">
										<xsl:value-of select="(string-length(chiave_ordinamento) - string-length(translate(chiave_ordinamento, '_', '')))" />
									</xsl:variable>

									<div class="bloccoIntervento"  style="width: {94 - $livello * 3}%; margin-left: {$livello * 3 + 1.5}%">
										<a name="intervento_{id_intervento}"></a>
										<xsl:if test="avatar != ''">
											<img src="../downloaddoc.php?tabella=utenti&amp;tipo=avatar&amp;id={id_utente}" class="avatar"/>
										</xsl:if>

										<div id="intervento_{id_intervento}">
											<xsl:call-template name="risolvi_emoticon">
												<xsl:with-param name="string" select="testo"/>
											</xsl:call-template>
										</div>
										<p />

										[ Pubblicato da: 
										<a href='vedi_utente.php?id_utente={id_utente}' id='nickname_{id_intervento}'>
											<xsl:value-of select="nickname"/>
										</a>
										]
										<br />
										[
										il: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
										alle: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
										]

										<!--302 = privilegio inserimento interventi tutti; 303 inserimento propri-->
										<xsl:if test="$dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_502 = 502 or 
														$dati_utente/privilegi/elemento_id_503 = 503">
											<a href="javascript:rispondiIntervento('{id_argomento}', '{id_intervento}')">
												<img src="../ui/img/street/Rispondi.gif" class="edit" alt="Rispondi" title="Rispondi" />
											</a>
										</xsl:if>

										<!--abilitazione bottoni modifica/cancella per chi ha i privilegi-->
										<!--304 = privilegio modifica tutti; 305 modifica propri-->
										<xsl:if test="$dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_504 = 504 or 
														($dati_utente/privilegi/elemento_id_505 = 505 and $dati_utente/id_utente = id_utente)">
											<a href="javascript:modificaIntervento('{id_argomento}', '{id_intervento}')">
												<img src="../ui/img/street/Modifica.gif" class="edit" alt="Modifica" title="Modifica"/>
											</a>
										</xsl:if>

										<!--306 = privilegio elimina tutti; 307 elimina propri-->
										<xsl:if test="tengo_famiglia = 0 and
														($dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_506 = 506 or 
														($dati_utente/privilegi/elemento_id_507 = 507 and $dati_utente/id_utente = id_utente)
														)
														">
											<a href="javascript:eliminaIntervento('{id_argomento}', '{id_intervento}')">
												<img src="../ui/img/street/Elimina.gif" class="edit" alt="Elimina" title="Elimina"/>
											</a>										
										</xsl:if>
									</div>

								</xsl:for-each>

								<!--bottoni di paginazione-->
								<xsl:call-template name="bottoni_paginazione" >
									<xsl:with-param name="rs" select="pagina/elementi/elemento[nome='interventi']/valore" />
									<xsl:with-param name="anchor" select="'interventi'" />
								</xsl:call-template>
							
								<xsl:if test="count(pagina/elementi/elemento[nome='interventi']/valore/riga)">
									<!--bottoni feeder-->
									<xsl:call-template name="blocco_feeder" />
								</xsl:if>
								
							</div> <!-- elencoInterventi -->
							
							<xsl:if test="not($dati_utente)">
								<xsl:call-template name="modulo_login">
									<xsl:with-param name="descr_azione">
										Per poter lasciare un intervento 
									</xsl:with-param>
								</xsl:call-template>
							</xsl:if>

							<xsl:if test="$dati_utente">
								<div class="modulo_intervento" id="modulo_intervento">
									<a name="modulo_intervento"></a>
									<h1 id="label_testo_intervento">Inserisci un intervento</h1>
									<xsl:if test="$dati_utente/privilegio_html_base = 1 and $dati_utente/privilegio_html_esteso = 0">	
										(sono ammessi i tag HTML &lt;b&gt; &lt;u&gt; &lt;i&gt;)
									</xsl:if>

									<div class="notizia_blocco">
										<!-- form intervento -->
										<form id="wamodulo" method="post" onsubmit="return validaForm(this)">
											<input type="hidden" id="wamodulo_nome_modulo" name="wamodulo_nome_modulo" value="wamodulo" />
											<input type="hidden" name="wamodulo_operazione" value="3" />
											<div class="campo">
												<textarea name='testo' class="mceEditor"></textarea>
											</div>
											<div class="campo">
												<input type='submit' name="cmd_invia" value='invia' />
												<input name="annulla_modifica" type='button' value='annulla' onclick="annullaModificaIntervento('{pagina/elementi/elemento[nome='argomento']/valore/riga/id_argomento}')" style="visibility:hidden" />
											</div>
										</form>
									</div>
								</div>

							</xsl:if> <!--dati utente-->
							
						</xsl:if> <!--no messaggio errore -->




					</div><!-- id="corpoCentrale" -->
					
					<xsl:call-template name="pagina_footer" />
	
				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->
		</body>
	</html>

</xsl:template>

<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<!-- *********************************************************************** -->
<xsl:template name="blocco_feeder">

	<xsl:variable name="dati_utente" select="exsl:node-set(/waapplicazione/pagina/elementi/elemento[nome='dati_utente']/valore)" />
	<xsl:variable name="argomento" select="exsl:node-set(/waapplicazione/pagina/elementi/elemento[nome='argomento']/valore/riga)" />

	<div class="bloccoFeeder">
		<img src="../ui/img/street/rss.png" border="0" onclick="location.href='../web_files/rss/interventi.{$argomento/id_argomento}.xml'" 
			alt="Iscriviti al feed per ricevere gli interventi su questo argomento di {/waapplicazione/titolo}" 
			title="Iscriviti al feed per ricevere gli interventi su questo argomento di {/waapplicazione/titolo}"  
			class="feeder" 
		/>
		<xsl:if test="$dati_utente/id_utente != ''">
			<!-- se l'utente è loggato diamo anche l'icona per la sottoscrizione via email -->
			<xsl:if test="$argomento/flag_sottoscrizione_via_email = 0">
				<img src="../ui/img/street/email.png" 
					 onclick="sottoscriviInterventiViaMail({$argomento/id_argomento})" 
					 alt="Ricevi via email gli interventi su questo argomento di {/waapplicazione/titolo}" 
					 title="Ricevi via email gli interventi su questo argomento di {/waapplicazione/titolo}" 
				/>
			</xsl:if>
			<xsl:if test="$argomento/flag_sottoscrizione_via_email = 1">
				<img src="../ui/img/street/noemail.png" 
					 onclick="smollaInterventiViaMail({$argomento/id_argomento})" 
					 alt="Termina di ricevere via email gli interventi su questo argomento di {/waapplicazione/titolo}" 
					 title="Termina di ricevere via email gli interventi su questo argomento di {/waapplicazione/titolo}" 
				/>
			</xsl:if>
		</xsl:if>
	</div>
	
</xsl:template>
	
</xsl:stylesheet>