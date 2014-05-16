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
					<div id="corpoCentrale">

						<!--facciamo vedere il contenuto della pagina solo se non  abbiamo ricevuito un errore-->
						<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']/valore)">

							<div class="articolo_blocco">
								<xsl:call-template name="blocco_articolo">
									<xsl:with-param name="articolo" select="$articolo" />
									<xsl:with-param name="tipo_posizione" select="'articolo'" />
									<xsl:with-param name="flag_testo" select="1" />
								</xsl:call-template>
							</div><!-- id="articolo_blocco" -->

							<div class="elencoCommenti" >
								<a name="commenti"></a>
								
								<!--bottoni feeder-->
								<xsl:call-template name="blocco_feeder" />
								
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

									<div class="bloccoCommento"  style="width: {94 - $livello * 3}%; margin-left: {$livello * 3 + 1.5}%">
										<a name="commento_{id_commento}"></a>
										<xsl:if test="avatar != ''">
											<img src="../downloaddoc.php?tabella=utenti&amp;tipo=avatar&amp;id={id_utente}" class="avatar"/>
										</xsl:if>

										<div id="commento_{id_commento}">
											<xsl:call-template name="risolvi_emoticon">
												<xsl:with-param name="string" select="testo"/>
											</xsl:call-template>
										</div>
										<p />

										[ Pubblicato da: 
										<a href='vedi_utente.php?id_utente={id_utente}' id='nickname_{id_commento}'>
											<xsl:value-of select="nickname"/>
										</a>
										]
										<br />
										[
										il: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
										alle: <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
										]

										<!--302 = privilegio inserimento commenti tutti; 303 inserimento propri-->
										<xsl:if test="$dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_302 = 302 or 
														$dati_utente/privilegi/elemento_id_303 = 303">
											<a href="javascript:rispondiCommento('{id_articolo}', '{id_commento}')">
												<img src="../ui/img/street/Rispondi.gif" class="edit" alt="Rispondi" title="Rispondi" />
											</a>
										</xsl:if>

										<!--abilitazione bottoni modifica/cancella per chi ha i privilegi-->
										<!--304 = privilegio modifica tutti; 305 modifica propri-->
										<xsl:if test="$dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_304 = 304 or 
														($dati_utente/privilegi/elemento_id_305 = 305 and $dati_utente/id_utente = id_utente)">
											<a href="javascript:modificaCommento('{id_articolo}', '{id_commento}')">
												<img src="../ui/img/street/Modifica.gif" class="edit" alt="Modifica" title="Modifica"/>
											</a>
										</xsl:if>

										<!--306 = privilegio elimina tutti; 307 elimina propri-->
										<xsl:if test="tengo_famiglia = 0 and
														($dati_utente/supervisore = 1 or
														$dati_utente/privilegi/elemento_id_306 = 306 or 
														($dati_utente/privilegi/elemento_id_307 = 307 and $dati_utente/id_utente = id_utente)
														)">
											<a href="javascript:eliminaCommento('{id_articolo}', '{id_commento}')">
												<img src="../ui/img/street/Elimina.gif" class="edit" alt="Elimina" title="Elimina"/>
											</a>										
										</xsl:if>
									</div>

								</xsl:for-each>

								<!--bottoni di paginazione-->
								<xsl:call-template name="bottoni_paginazione" >
									<xsl:with-param name="rs" select="pagina/elementi/elemento[nome='commenti']/valore" />
									<xsl:with-param name="anchor" select="'commenti'" />
								</xsl:call-template>

								<xsl:if test="count(pagina/elementi/elemento[nome='commenti']/valore/riga)">
									<!--bottoni feeder-->
									<xsl:call-template name="blocco_feeder" />
								</xsl:if>
								
							</div>	<!-- elencoCommenti -->

							<xsl:if test="not($dati_utente)">
								<xsl:call-template name="modulo_login">
									<xsl:with-param name="descr_azione">
										Per poter lasciare un commento 
									</xsl:with-param>
								</xsl:call-template>
							</xsl:if>

							<xsl:if test="$dati_utente">
								<div class="modulo_commento" id="modulo_commento">
									<a name="modulo_commento"></a>
									<h1 id="label_testo_commento">Inserisci un commento</h1>
									<xsl:if test="$dati_utente/privilegio_html_base = 1 and $dati_utente/privilegio_html_esteso = 0">	
										(sono ammessi i tag HTML &lt;b&gt; &lt;u&gt; &lt;i&gt;)
									</xsl:if>

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
	<xsl:variable name="articolo" select="exsl:node-set(/waapplicazione/pagina/elementi/elemento[nome='articolo']/valore/riga)" />
	
	<div class="bloccoFeeder">
		<img src="../ui/img/street/rss.png" border="0" onclick="location.href='../web_files/rss/commenti.{$articolo/id_articolo}.xml'" 
			alt="Iscriviti al feed per ricevere i commenti a questo articolo di {/waapplicazione/titolo}" 
			title="Iscriviti al feed per ricevere i commenti a questo articolo di {/waapplicazione/titolo}"  
			class="feeder" 
		/>
		<xsl:if test="$dati_utente/id_utente != ''">
			<!-- se l'utente è loggato diamo anche l'icona per la sottoscrizione via email -->
			<xsl:if test="$articolo/flag_sottoscrizione_via_email = 0">
				<img src="../ui/img/street/email.png" 
					 onclick="sottoscriviCommentiViaMail({$articolo/id_articolo})" 
					 alt="Ricevi via email i commenti di questo articolo di {/waapplicazione/titolo}" 
					 title="Ricevi via email i commenti di questo articolo di {/waapplicazione/titolo}" 
				/>
			</xsl:if>
			<xsl:if test="$articolo/flag_sottoscrizione_via_email = 1">
				<img src="../ui/img/street/noemail.png" 
					 onclick="smollaCommentiViaMail({$articolo/id_articolo})" 
					 alt="Termina di ricevere via email i commenti di questo articolo di {/waapplicazione/titolo}" 
					 title="Termina di ricevere via email i commenti di questo articolo di {/waapplicazione/titolo}" 
				/>
			</xsl:if>
		</xsl:if>
	</div>

</xsl:template>

	
</xsl:stylesheet>