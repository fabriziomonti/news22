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

	<html>
		<xsl:call-template name="pagina_head_html" />
		<body>
			<div id="pagina">
				<div id="contenitore">
					<xsl:call-template name="pagina_header" />
					
					
					<!-- COLONNA SX -->
					<div id="colonnaSx">

						<xsl:if test="not(pagina/elementi/elemento[nome='messaggio']) and not(pagina/elementi/elemento[nome='messaggio_ok'])">
							<!-- REGISTRAZIONE -->
							<h1>Registrazione</h1>
							<div class="articolo_blocco">

								<p>
									Per poter lasciare un commento o intervenire nel forum devi essere registrato.
								</p>

								<form id="wamodulo" method="post" onsubmit="return validaForm(this)">
									<input type="hidden" id="wamodulo_nome_modulo" name="wamodulo_nome_modulo" value="wamodulo" />
									<input type="hidden" name="wamodulo_operazione" value="3" />
									<label>Indirizzo email *</label>
										(sara' la tua login e non sarà mai pubblicata)

									<div class="campo"><input type="text" name="email" /></div>

									<label>Scegli un tuo nickname *</label>
									(puo' anche essere il tuo vero nome, se vuoi, ma occhio alla privacy)
									<div class="campo"><input type="text" name="nickname" /></div>	

									<label>Regolamento del sito</label>
									<div class="campo">
										<textarea name="condizioni_servizio" cols='88' rows='10' onKeyPress='return false'>
											bla bla bla....
										</textarea>
									</div>	

									<label>Confermo di avere letto il regolamento *</label>
									<div class="campo"><input type="checkbox" name="ho_letto_condizioni_servizio" /></div>	

									<label>Informativa trattamento dati</label>
									<div class="campo">
										<textarea name="informativa_privacy" cols='88' rows='10' onKeyPress='return false'>Informativa art. 13 D.Lgs. 196/2003

Si informa il sottoscrittore della presente che il decreto legislativo n. 196/2003 prevede la tutela delle persone e di altri soggetti rispetto al trattamento dei dati personali. Secondo le leggi indicate, tale trattamento sarà improntato ai principi di correttezza, liceità e trasparenza tutelando la riservatezza e i diritti del sottoscrittore. Le seguenti informazioni vengono fornite ai sensi dell’articolo 13 del decreto legislativo n. 196/2003.
Il trattamento che intendiamo effettuare:

a)	ha la finalità di concludere, gestire ed eseguire i servizi richiesti; 
b)  di assolvere agli obblighi di legge o agli altri adempimenti richiesti dalle competenti Autorità;
b)	sarà effettuato con le seguenti modalità: informatizzato/manuale;
c)	salvo quanto strettamente necessario per la corretta esecuzione del servizio, i dati non saranno comunicati ad altri soggetti, se non chiedendoLe espressamente il consenso. 

Informiamo ancora che la comunicazione dei dati è indispensabile ma non obbligatoria e l'eventuale rifiuto non ha alcuna conseguenza ma potrebbe comportare il mancato puntuale adempimento delle obbligazioni assunte da WebAppls di Monti Fabrizio per la fornitura del servizio da Lei richiesto. Il titolare del trattamento è WebAppls di Monti Fabrizio con sede legale in Via Raveda 465/M - 40018 - San Pietro in Casale (BO) - PI 02130751205, alla quale può rivolgersi per far valere i Suoi diritti così come previsto dall'articolo 7 del decreto legislativo n. 196/2003, che riportiamo di seguito per esteso: 

Art. 7
Diritto di accesso ai dati personali ed altri diritti
1. L'interessato ha diritto di ottenere la conferma dell'esistenza o meno di dati personali che lo riguardano, anche se non ancora registrati, e la loro comunicazione in forma intelligibile.
2. L'interessato ha diritto di ottenere l'indicazione:
a) dell'origine dei dati personali;
b) delle finalità e modalità del trattamento;
c) della logica applicata in caso di trattamento effettuato con l'ausilio di strumenti elettronici;
d) degli estremi identificativi del titolare, dei responsabili e del rappresentante designato ai sensi dell'articolo 5, comma 2;
e) dei soggetti o delle categorie di soggetti ai quali i dati personali possono essere comunicati o che possono venirne a conoscenza in qualità di rappresentante designato nel territorio dello Stato, di responsabili o incaricati.
3. L'interessato ha diritto di ottenere:
a) l'aggiornamento, la rettificazione ovvero, quando vi ha interesse, l'integrazione dei dati;
b) la cancellazione, la trasformazione in forma anonima o il blocco dei dati trattati in violazione di legge, compresi quelli di cui non è necessaria la conservazione in relazione agli scopi per i quali i dati sono stati raccolti o successivamente trattati;
c) l'attestazione che le operazioni di cui alle lettere a) e b) sono state portate a conoscenza, anche per quanto riguarda il loro contenuto, di coloro ai quali i dati sono stati comunicati o diffusi, eccettuato il caso in cui tale adempimento si rivela impossibile o comporta un impiego di mezzi manifestamente sproporzionato rispetto al diritto tutelato.
4. L'interessato ha diritto di opporsi, in tutto o in parte:
a) per motivi legittimi al trattamento dei dati personali che lo riguardano, ancorché pertinenti allo scopo della raccolta;
b) al trattamento di dati personali che lo riguardano a fini di invio di materiale pubblicitario o di vendita diretta o per il compimento di ricerche di mercato o di comunicazione commerciale.

Formula di consenso

Acquisite le informazioni che precedono, rese ai sensi dell'art. 13 del D.Lgs. 196/2003, consento al trattamento dei miei dati come sopra descritto.
										</textarea>
									</div>	

									<label>Confermo di avere letto l'informativa *</label>
									<div class="campo"><input type="checkbox" name="ho_letto_informativa_privacy" /></div>	

									<label>Codice di controllo *</label>
									<div class="campo">
										<img src="../walibs3/wamodulo/uis/wa_default/img/captcha.php?k={pagina/elementi/elemento[nome='captcha_key']/valore}" />
										<input type="hidden" name="k_captcha" value="{pagina/elementi/elemento[nome='captcha_key']/valore}" />
										<xsl:text> </xsl:text>
										<input type='text' name='captcha' maxlength='5' size='5' />
									</div>	

									<div class="campo"><input type="submit" name="campo" value="Registrati"/></div>	
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