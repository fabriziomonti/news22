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

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />
					
					
					<!-- COLONNA SX -->
					<div id="colonnaSx">
						<xsl:if test="$dati_utente">

							<h1>Il tuo profilo</h1>
							<xsl:if test="$dati_utente/avatar != ''">
								<img src="../downloaddoc.php?tabella=utenti&amp;tipo=avatar&amp;id={$dati_utente/id_utente}" style="margin-bottom: 20px;"/>
								<br />
							</xsl:if>

							<div class="articolo_blocco">

								<form id="wamodulo" method="post" onsubmit="return validaForm(this)" enctype="multipart/form-data" >
									<input type="hidden" id="wamodulo_nome_modulo" name="wamodulo_nome_modulo" value="wamodulo" />
									<input type="hidden" name="wamodulo_operazione" value="3" />

									<label>Nickname </label>
									<div class="campo">
										<input value="{$dati_utente/nickname}" type="text" name="nickname" disabled="disabled"/>
									</div>	

									<label>Indirizzo email *</label>
									<div class="campo">
										<input  value="{$dati_utente/email}" type="text" name="email" />
									</div>

									<label>Password e conferma password</label>
									solo se vuoi modificarla; nel caso:
									<ul>
										<li>8~12 caratteri</li>
										<li>solo lettere e numeri</li>
										<li>almeno una maiuscola</li>
										<li>almeno una minuscola</li>
										<li>almeno un numero)</li>
									</ul>
									<div class="campo">
										<input  value="" type="password" name="pwd" maxlength="12"/>
										<input  value="" type="password" name="pwd_conferma"  maxlength="12" style="margin-left: 10px;"/>
									</div>

									<label>Il tuo avatar</label>
									max 100x100
									<div class="campo">
										<input  type="file" name="avatar" />
									</div>

									<label>Racconta un po' di te, se vuoi...</label>
									<div class="campo">
										<textarea class="mceEditor" name="descrizione"><xsl:value-of select="$dati_utente/descrizione" /></textarea>
									</div>

									<div class="campo">
										<input type="submit" value="Registra modifiche"/>
										<xsl:text> </xsl:text>
										<input type="button" name="campo" value="Elimina utenza" onclick="confermaEliminaUtenza()"/>
									</div>	
								</form>
							</div>
						</xsl:if>

					</div><!-- id="colonnaSx" -->
					
					<xsl:call-template name="pagina_footer" />
	
				</div><!-- id="contenitore" -->
			
			</div><!-- id="pagina" -->

		</body>
	</html>

</xsl:template>

	
</xsl:stylesheet>