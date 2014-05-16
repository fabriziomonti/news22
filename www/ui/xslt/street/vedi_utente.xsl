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
	
	<xsl:variable name="utente_visualizzato" select="exsl:node-set(pagina/elementi/elemento[nome='utente_visualizzato']/valore/riga)" />
	<xsl:variable name="articoli" select="exsl:node-set(pagina/elementi/elemento[nome='articoli']/valore)" />
	<xsl:variable name="argomenti" select="exsl:node-set(pagina/elementi/elemento[nome='argomenti']/valore)" />
	<xsl:variable name="commenti" select="exsl:node-set(pagina/elementi/elemento[nome='commenti']/valore)" />
	<xsl:variable name="interventi" select="exsl:node-set(pagina/elementi/elemento[nome='interventi']/valore)" />
	<xsl:variable name="max_char_titoli" >30</xsl:variable>
	<xsl:variable name="max_char_commenti" >60</xsl:variable>

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />

					<!-- COLONNA SX -->
					<div id="corpoCentrale">

						<!--facciamo vedere il contenuto della pagina solo se non  abbiamo ricevuito un errore-->
						<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']/valore)">
							
							<div class="articolo_blocco">
								<h1>
									Il profilo di 
									<xsl:value-of select="$utente_visualizzato/nickname" />
								</h1>
								<xsl:if test="$utente_visualizzato/avatar != ''">
									<div>
										<img src="../downloaddoc.php?tabella=utenti&amp;tipo=avatar&amp;id={$utente_visualizzato/id_utente}" style="margin-bottom: 1.2em; "/>
									</div>
								</xsl:if>
								<xsl:if test="$utente_visualizzato/supervisore = '1'">
									supervisore
								</xsl:if>
								iscritto dal <xsl:value-of select="substring-before($utente_visualizzato/data_ora_creazione, ' ')" />
								<br />

								<xsl:if test="$utente_visualizzato/descrizione != ''">
									<h1 style="margin-top: 3em;">
										Dice di sè
									</h1>
									<xsl:call-template name="risolvi_emoticon">
										<xsl:with-param name="string" select="$utente_visualizzato/descrizione"/>
									</xsl:call-template>
								</xsl:if>
								<br />
							</div>

							<!--blocco articoli scritti dall'utente-->
							<xsl:if test="$articoli/riga">
								<div class="elencoArticoliUtente">
									<h1>
										Gli articoli scritti da <xsl:value-of select="$utente_visualizzato/nickname" /> (<xsl:value-of select="$articoli/nr_righe_senza_limite" />)
									</h1>

									<a name="articoli"></a>
									<table>
										<xsl:for-each select="$articoli/riga">
											<tr>
												<td>
													<a href="articolo.php?id_articolo={id_articolo}">
														<xsl:value-of select="substring(titolo, 1, $max_char_commenti)" />
														<xsl:if test="string-length(titolo) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td>
													in
													<a href="index.php?id_categoria_articolo={id_categoria_articolo}">
														<xsl:value-of disable-output-escaping="yes" select="nome_categoria" />
													</a>
												</td>
												<td style="text-align: right">
													il <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
												</td>
											</tr>
										</xsl:for-each>
									</table>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$articoli" />
										<xsl:with-param name="anchor" select="'articoli'" />
									</xsl:call-template>

								</div>
							</xsl:if>

							<!--blocco argomenti scritti dall'utente-->
							<xsl:if test="$argomenti/riga">
								<div class="elencoArgomentiUtente">
									<h1>
										Gli argomenti proposti da <xsl:value-of select="$utente_visualizzato/nickname" /> (<xsl:value-of select="$argomenti/nr_righe_senza_limite" />)
									</h1>

									<a name="argomenti"></a>
									<table>
										<xsl:for-each select="$argomenti/riga">
											<tr>
												<td>
													<a href="argomento.php?id_argomento={id_argomento}">
														<xsl:value-of select="substring(titolo, 1, $max_char_commenti)" />
														<xsl:if test="string-length(titolo) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td>
													in
													<a href="forum.php?id_categoria_argomento={id_categoria_argomento}">
														<xsl:value-of select="substring(nome_categoria, 1, $max_char_titoli)" />
														<xsl:if test="string-length(nome_categoria) &gt; $max_char_titoli">
															...
														</xsl:if>
													</a>
												</td>
												<td style="text-align: right">
													il <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
												</td>
											</tr>
										</xsl:for-each>
									</table>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$argomenti" />
										<xsl:with-param name="anchor" select="'argomenti'" />
									</xsl:call-template>

								</div>
							</xsl:if>

							<!--blocco commenti dell'utente-->
							<xsl:if test="$commenti/riga">
								<div class="elencoCommentiUtente">
									<h1>
										I commenti scritti da <xsl:value-of select="$utente_visualizzato/nickname" /> (<xsl:value-of select="$commenti/nr_righe_senza_limite" />)
									</h1>

									<a name="commenti"></a>
									<table>
										<xsl:for-each select="$commenti/riga">
											<tr>
												<td colspan="2">
													<a href="articolo.php?id_articolo={id_articolo}&amp;pag_commenti={nr_pagina}#commento_{id_commento}">
														<xsl:value-of select="substring(testo, 1, $max_char_commenti)" />
														<xsl:if test="string-length(testo) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td style="text-align: right">
													il <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
												</td>
											</tr>
											<tr>
												<td>
													in 
													<a href="articolo.php?id_articolo={id_articolo}">
														<xsl:value-of select="substring(titolo_articolo, 1, $max_char_commenti)" />
														<xsl:if test="string-length(titolo_articolo) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td>
													in
													<a href="index.php?id_categoria_articolo={id_categoria_articolo}">
														<xsl:value-of select="substring(nome_categoria, 1, $max_char_titoli)" />
														<xsl:if test="string-length(nome_categoria) &gt; $max_char_titoli">
															...
														</xsl:if>
													</a>
												</td>
												<td style="text-align: right">
													del <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
												</td>
											</tr>
											<tr>
												<td style="height: 1.2em;"> </td>
											</tr>
										</xsl:for-each>
									</table>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$commenti" />
										<xsl:with-param name="anchor" select="'commenti'" />
									</xsl:call-template>
								</div>
							</xsl:if>

							<!--blocco interventi dell'utente-->
							<xsl:if test="$interventi/riga">
								<div class="elencoInterventiUtente">
									<h1>
										Gli interventi di <xsl:value-of select="$utente_visualizzato/nickname" /> (<xsl:value-of select="$interventi/nr_righe_senza_limite" />)
									</h1>

									<a name="interventi"></a>
									<table>
										<xsl:for-each select="$interventi/riga">
											<tr>
												<td colspan="2">
													<a href="argomento.php?id_argomento={id_argomento}&amp;pag_interventi={nr_pagina}#intervento_{id_intervento}">
														<xsl:value-of select="substring(testo, 1, $max_char_commenti)" />
														<xsl:if test="string-length(testo) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td style="text-align: right">
													il <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
												</td>
											</tr>
											<tr>
												<td>
													in 
													<a href="argomento.php?id_argomento={id_argomento}">
														<xsl:value-of select="substring(titolo_argomento, 1, $max_char_commenti)" />
														<xsl:if test="string-length(titolo_argomento) &gt; $max_char_commenti">
															...
														</xsl:if>
													</a>
												</td>
												<td>
													in
													<a href="forum.php?id_categoria_argomento={id_categoria_argomento}">
														<xsl:value-of select="substring(nome_categoria, 1, $max_char_titoli)" />
														<xsl:if test="string-length(nome_categoria) &gt; $max_char_titoli">
															...
														</xsl:if>
													</a>
												</td>
												<td style="text-align: right">
													del <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
													alle <xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
												</td>
											</tr>
											<tr>
												<td style="height: 1.2em;"> </td>
											</tr>
										</xsl:for-each>
									</table>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$interventi" />
										<xsl:with-param name="anchor" select="'interventi'" />
									</xsl:call-template>
								</div>
							</xsl:if>


							
						</xsl:if> <!--no messaggio errore -->

					</div><!-- id="corpoCentrale" -->
					
					<xsl:call-template name="pagina_footer" />
	
				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->
		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>