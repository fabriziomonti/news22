<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY nbsp "&#160;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/wamodulo_template_comuni.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/areatesto.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/bottone.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/captcha.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/caricafile.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/cfpi.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/cornice.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/data.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/dataora.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/email.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/etichetta.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/intero.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/logico.xsl"/>
<!--<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/multiselezione.xsl"/>-->
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/multiselezione_checkbox.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/noncontrollo.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/opzione.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/ora.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/password.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/selezione.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/testo.xsl"/>
<xsl:import href="../../../walibs3/wamodulo/uis/wa_default/xslt/valuta.xsl"/>

<xsl:import href="modulo.xsl"/>
<xsl:import href="../../../wamodulo_ext/ui/xslt/selezione_ext.xsl"/>
<xsl:import href="../../../wamodulo_ext/ui/xslt/areatesto_ext.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

</xsl:stylesheet>