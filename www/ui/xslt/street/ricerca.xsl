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

		<html>
			<xsl:call-template name="pagina_head_html" />
			<body>
				<div id="pagina">
					<div id="contenitore">
						<xsl:call-template name="pagina_header" />
					
					
						<!-- COLONNA SX -->
						<div id="colonnaSx">

							<!--facciamo vedere il contenuto della pagina solo se non  abbiamo ricevuito un errore-->
							<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']/valore)">

								<xsl:if test="pagina/elementi/elemento[nome='articoli']/valore/riga">
									<!--blocco articoli scritti dall'utente-->
									<xsl:variable name="articoli" select="exsl:node-set(pagina/elementi/elemento[nome='articoli']/valore)" />
									<div class="elencoArticoliUtente">
										<h1>
											Articoli (<xsl:value-of select="$articoli/nr_righe_senza_limite" />)
										</h1>

										<table>
											<xsl:for-each select="$articoli/riga">
												<tr>
													<td>
														<a href="articolo.php?id_articolo={id_articolo}">
															<xsl:value-of select="substring(titolo, 1, 90)" />
															<xsl:if test="string-length(titolo) &gt; 90">
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
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
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

								<xsl:if test="pagina/elementi/elemento[nome='argomenti']/valore/riga">
									<!--blocco argomenti scritti dall'utente-->
									<xsl:variable name="argomenti" select="exsl:node-set(pagina/elementi/elemento[nome='argomenti']/valore)" />
									<div class="elencoArgomentiUtente">
										<h1>
											Argomenti (<xsl:value-of select="$argomenti/nr_righe_senza_limite" />)
										</h1>

										<table>
											<xsl:for-each select="$argomenti/riga">
												<tr>
													<td>
														<a href="argomento.php?id_argomento={id_argomento}">
															<xsl:value-of select="substring(titolo, 1, 90)" />
															<xsl:if test="string-length(titolo) &gt; 90">
																...
															</xsl:if>
														</a>
													</td>
													<td>
														in
														<a href="forum.php?id_categoria_argomento={id_categoria_argomento}">
															<xsl:value-of disable-output-escaping="yes" select="nome_categoria" />
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
									
										<!--bottoni di paginazione-->
										<xsl:call-template name="bottoni_paginazione" >
											<xsl:with-param name="rs" select="$argomenti" />
											<xsl:with-param name="anchor" select="'argomenti'" />
										</xsl:call-template>

									</div>

								</xsl:if>

								<xsl:if test="pagina/elementi/elemento[nome='commenti']/valore/riga">
									<!--blocco commenti -->
									<xsl:variable name="commenti" select="exsl:node-set(pagina/elementi/elemento[nome='commenti']/valore)" />
									<div class="elencoCommentiUtente">
										<h1>
											Commenti (<xsl:value-of select="$commenti/nr_righe_senza_limite" />)
										</h1>

										<a name="commenti" />
										<table>
											<xsl:for-each select="$commenti/riga">
												<tr>
													<td>
														<a href="articolo.php?id_articolo={id_articolo}&amp;pag_commenti={nr_pagina}#commento_{id_commento}">
															<xsl:value-of select="substring(testo, 1, 90)" />
															<xsl:if test="string-length(testo) &gt; 90">
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
															<xsl:value-of select="substring(titolo_articolo, 1, 90)" />
															<xsl:if test="string-length(titolo_articolo) &gt; 90">
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
														del 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[1]"/>
														alle 
														<xsl:value-of select="str:tokenize(data_ora_inizio_pubblicazione, ' ')[2]"/>
													</td>
												</tr>
												<tr>
													<td colspan="3" style="height: 20px;"> </td>
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

								<xsl:if test="pagina/elementi/elemento[nome='interventi']/valore/riga">
									<!--blocco interventi -->
									<xsl:variable name="interventi" select="exsl:node-set(pagina/elementi/elemento[nome='interventi']/valore)" />
									<div class="elencoInterventiUtente">
										<h1>
											Interventi (<xsl:value-of select="$interventi/nr_righe_senza_limite" />)
										</h1>

										<a name="interventi" />
										<table>
											<xsl:for-each select="$interventi/riga">
												<tr>
													<td>
														<a href="argomento.php?id_argomento={id_argomento}&amp;pag_interventi={nr_pagina}#intervento_{id_intervento}">
															<xsl:value-of select="substring(testo, 1, 90)" />
															<xsl:if test="string-length(testo) &gt; 90">
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
															<xsl:value-of select="substring(titolo_argomento, 1, 90)" />
															<xsl:if test="string-length(titolo_argomento) &gt; 90">
																...
															</xsl:if>
														</a>
													</td>
													<td>
														in
														<a href="forum.php?id_categoria_argomento={id_categoria_argomento}">
															<xsl:value-of disable-output-escaping="yes" select="nome_categoria" />
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
													<td colspan="3" style="height: 20px;"> </td>
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


							</xsl:if>


							<!--modulo inserimento parametri-->
							<hr />
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
										</input>
									</xsl:for-each>
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
										</input>
									</xsl:for-each>
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

						</div><!-- id="colonnaSx" -->
					
						<xsl:call-template name="pagina_footer" />
	
					</div><!-- id="contenitore" -->
			
				</div><!-- id="pagina" -->

			</body>
		</html>

	</xsl:template>

	
</xsl:stylesheet>