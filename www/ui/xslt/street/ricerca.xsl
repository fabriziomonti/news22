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

		<xsl:variable name="request" select="exsl:node-set(pagina/elementi/elemento[nome='request']/valore)" />
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

								<xsl:if test="pagina/elementi/elemento[nome='articoli']/valore/riga">
									<!--blocco articoli scritti dall'utente-->
									<xsl:variable name="articoli" select="exsl:node-set(pagina/elementi/elemento[nome='articoli']/valore)" />
									<div class="elencoArticoliUtente">
										<h1 id="label_testo_commento">
											Articoli (<xsl:value-of select="$articoli/nr_righe_senza_limite" />)
										</h1>

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
															<xsl:value-of select="substring(nome_categoria, 1, $max_char_titoli)" />
															<xsl:if test="string-length(nome_categoria) &gt; $max_char_titoli">
																...
															</xsl:if>
														</a>
													</td>
													<td style="text-align: right">
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
													</td>
												</tr>
											</xsl:for-each>
										</table>
									
									</div>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$articoli" />
										<xsl:with-param name="anchor" select="'articoli'" />
									</xsl:call-template>

								</xsl:if>

								<xsl:if test="pagina/elementi/elemento[nome='argomenti']/valore/riga">
									<!--blocco argomenti scritti dall'utente-->
									<xsl:variable name="argomenti" select="exsl:node-set(pagina/elementi/elemento[nome='argomenti']/valore)" />
									<div class="elencoArgomentiUtente">
										<h1 id="label_testo_commento">
											Argomenti (<xsl:value-of select="$argomenti/nr_righe_senza_limite" />)
										</h1>

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
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
													</td>
												</tr>
											</xsl:for-each>
										</table>
									
									</div>

									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$argomenti" />
										<xsl:with-param name="anchor" select="'argomenti'" />
									</xsl:call-template>

								</xsl:if>

								<xsl:if test="pagina/elementi/elemento[nome='commenti']/valore/riga">
									<!--blocco commenti -->
									<xsl:variable name="commenti" select="exsl:node-set(pagina/elementi/elemento[nome='commenti']/valore)" />
									<div class="elencoCommentiUtente">
										<h1 id="label_testo_commento">
											Commenti (<xsl:value-of select="$commenti/nr_righe_senza_limite" />)
										</h1>

										<a name="commenti"></a>
										<table>
											<xsl:for-each select="$commenti/riga">
												<tr>
													<td>
														<a href="articolo.php?id_articolo={id_articolo}&amp;pag_commenti={nr_pagina}#commento_{id_commento}">
															<xsl:value-of select="substring(testo, 1, $max_char_commenti)" />
															<xsl:if test="string-length(testo) &gt; $max_char_commenti">
																...
															</xsl:if>
														</a>
													</td>
													<td>
														di 
														<a href="vedi_utente.php?id_utente={id_utente}">
															<xsl:value-of select="nickname" />
														</a>
													</td>
													<td style="text-align: right">
														del 
														<xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
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
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
													</td>
												</tr>
												<tr>
													<td colspan="3" style="height: 1em;"> </td>
												</tr>
											</xsl:for-each>
										</table>

									</div>
									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$commenti" />
										<xsl:with-param name="anchor" select="'commenti'" />
									</xsl:call-template>

								</xsl:if>

								<xsl:if test="pagina/elementi/elemento[nome='interventi']/valore/riga">
									<!--blocco interventi -->
									<xsl:variable name="interventi" select="exsl:node-set(pagina/elementi/elemento[nome='interventi']/valore)" />
									<div class="elencoInterventiUtente">
										<h1 id="label_testo_commento">
											Interventi (<xsl:value-of select="$interventi/nr_righe_senza_limite" />)
										</h1>

										<a name="interventi"></a>
										<table>
											<xsl:for-each select="$interventi/riga">
												<tr>
													<td>
														<a href="argomento.php?id_argomento={id_argomento}&amp;pag_interventi={nr_pagina}#intervento_{id_intervento}">
															<xsl:value-of select="substring(testo, 1, $max_char_commenti)" />
															<xsl:if test="string-length(testo) &gt; $max_char_commenti">
																...
															</xsl:if>
														</a>
													</td>
													<td>
														di 
														<a href="vedi_utente.php?id_utente={id_utente}">
															<xsl:value-of select="nickname" />
														</a>
													</td>
													<td style="text-align: right">
														del 
														<xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_creazione, ' ')[2]"/>
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
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
													</td>
												</tr>
												<tr>
													<td colspan="3" style="height: 1em;"> </td>
												</tr>
											</xsl:for-each>
										</table>

									</div>
									<!--bottoni di paginazione-->
									<xsl:call-template name="bottoni_paginazione" >
										<xsl:with-param name="rs" select="$interventi" />
										<xsl:with-param name="anchor" select="'interventi'" />
									</xsl:call-template>

								</xsl:if>


							</xsl:if>


							<!--modulo inserimento parametri-->
							<div class="modulo_ricerca">
								<a name="modulo_ricerca"></a>
								<form id="wamodulo" method="get" onsubmit="return validaForm(this)" >

									<label>
										Categorie articoli
									</label>
									<div class="campo">
										<xsl:for-each select="pagina/elementi/elemento[nome='categorie_articoli']/valore/riga">
											<a href="index.php?id_categoria_articolo={id_categoria_articolo}">
												<xsl:value-of select="nome" />
											</a>
											<input type="checkbox" name="id_categoria_articolo[{id_categoria_articolo}]" >
												<xsl:variable name="request_id_categoria_articolo" select="concat('elemento_id_', id_categoria_articolo)" />
												<xsl:if test="$request/id_categoria_articolo/*[name() = $request_id_categoria_articolo]">
													<xsl:attribute name="checked">
														checked
													</xsl:attribute>
												</xsl:if>
												<xsl:if test="$request/tutte_categorie_articoli">
													<xsl:attribute name="disabled">
														disabled
													</xsl:attribute>
												</xsl:if>
											</input>
										</xsl:for-each>
										Tutte
										<input type="checkbox" name="tutte_categorie_articoli" onclick="tutte_categorie_articoli_click(this)">
											<xsl:if test="$request/tutte_categorie_articoli">
												<xsl:attribute name="checked">
													checked
												</xsl:attribute>
											</xsl:if>
										</input>
									</div>	

									<label>
										Categorie argomenti
									</label>
									<div class="campo">
										<xsl:for-each select="pagina/elementi/elemento[nome='categorie_argomenti']/valore/riga">
											<a href="forum.php?id_categoria_argomento={id_categoria_argomento}">
												<xsl:value-of select="nome" />
											</a>
											<input type="checkbox" name="id_categoria_argomento[{id_categoria_argomento}]" >
												<xsl:variable name="request_id_categoria_argomento" select="concat('elemento_id_', id_categoria_argomento)" />
												<xsl:if test="$request/id_categoria_argomento/*[name() = $request_id_categoria_argomento]">
													<xsl:attribute name="checked">
														checked
													</xsl:attribute>
												</xsl:if>
												<xsl:if test="$request/tutte_categorie_argomenti">
													<xsl:attribute name="disabled">
														disabled
													</xsl:attribute>
												</xsl:if>
											</input>
										</xsl:for-each>
										Tutte
										<input type="checkbox" name="tutte_categorie_argomenti" onclick="tutte_categorie_argomenti_click(this)">
											<xsl:if test="$request/tutte_categorie_argomenti">
												<xsl:attribute name="checked">
													checked
												</xsl:attribute>
											</xsl:if>
										</input>
									</div>	

									<label>
										<table class="allineamentoControlli">
											<tr>
												<td>Commenti</td>
												<td>Interventi</td>
											</tr>
										</table>
									</label>
									<div class="campo">
										<table class="allineamentoControlli">
											<tr>
												<td>
													<input type="checkbox" name="flag_commenti" >
														<xsl:if test="$request/flag_commenti">
															<xsl:attribute name="checked">
																checked
															</xsl:attribute>
														</xsl:if>
													</input>							
												</td>
												<td>
													<input type="checkbox" name="flag_interventi" >
														<xsl:if test="$request/flag_interventi">
															<xsl:attribute name="checked">
																checked
															</xsl:attribute>
														</xsl:if>
													</input>							
												</td>
											</tr>
										</table>
									</div>	

									<label>
										Utente
									</label>
									<div class="campo">
										<input type="text" name="nickname" value="{$request/nickname}"/>
									</div>	

									<label>
										<table class="allineamentoControlli">
											<tr>
												<td>Dalla data</td>
												<td>Alla data</td>
											</tr>
										</table>
									</label>
									<div class="campo">
										<table class="allineamentoControlli">
											<tr>
												<td>
													<input type="text" name="dalla_data"  value="{$request/dalla_data}"/>
												</td>
												<td>
													<input type="text" name="alla_data"  value="{$request/alla_data}"/>
												</td>
											</tr>
										</table>
									</div>	

									<label>
										<table class="allineamentoControlli">
											<tr>
												<td>Espressione</td>
												<td>Tag</td>
											</tr>
										</table>
									</label>
									<div class="campo">
										<table class="allineamentoControlli">
											<tr>
												<td>
													<input type="text" name="espressione"   value="{$request/espressione}"/>
												</td>
												<td>
													<input type="text" name="tag"   value="{$request/tag}"/>
												</td>
											</tr>
										</table>
									</div>	

									<div class="campo">
										<input type="submit" value="Cerca"/>
									</div>	

								</form>

							</div><!-- modulo_ricerca -->
							
						</div><!-- id="corpoCentrale" -->
					
						<xsl:call-template name="pagina_footer" />
	
					</div><!-- id="contenitore" -->
			
				</div><!-- id="pagina" -->

			</body>
		</html>

	</xsl:template>

	
</xsl:stylesheet>