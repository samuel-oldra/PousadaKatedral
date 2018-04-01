<html>
<head>
<style type="text/css">
<!--
.style1 {font-size: 10px}
body,td,th {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #999999;
}
body {
    background-color: #000000;
}
#enviar {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    background-color: #003366;
    color: #D4D0C8;
    font-weight: normal;
    border-top-style: double;
    border-right-style: double;
    border-bottom-style: double;
    border-left-style: double;
    font-size: 10px;
}
#emails {
}
.style2 {font-size: 9px; }
-->
</style>
<title>:: r00t_System group 1999-2016 by AFROM4N ::</title></head>
<body>

  <tr>
    <td width="368" height="346" align="center"><p>&nbsp;</p>
    <form name="form1" method="post" action="">
      <table width="324" border="0" align="center" bordercolor="#003300">
        <tr>
          <td colspan="2" rowspan="3" valign="top" bgcolor="#550000"><div align="justify" class="style1">Sistema de envio de emails. Selecione ao lado o sistema de envio, caso o sistema escolhido n&atilde;o funcione tente os  outros. </div></td>
          <td align="center" bgcolor="#550000"><span class="style1"><strong>SMTP</strong></span></td>
          <td align="center" bgcolor="#003366"><span class="style1"><strong><strong>
            <input name="radiobutton" type="radio" value="smtp" checked>
          </strong></strong></span></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#550000"><span class="style1"><strong><strong><strong>MAIL</strong></strong></strong></span></td>
          <td align="center" bgcolor="#003366"><span class="style1"><strong><strong><strong>
            <input name="radiobutton" type="radio" value="mail">
          </strong></strong></strong></span></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#550000"><span class="style1"><strong><strong>SENDMAIL</strong></strong></span></td>
          <td align="center" bgcolor="#003366"><span class="style1"><strong><strong>
            <input name="radiobutton" type="radio" value="sendmail">
          </strong></strong></span></td>
        </tr>
        <tr>
          <td width="160" align="center" bgcolor="#550000"><input name="nome" type="text" id="nome" value="" size="26"></td>
          <td colspan="3" align="center" bgcolor="#550000"><input name="remetente" type="text" id="remetente" value="" size="26"></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#550000"><input name="assunto" type="text" id="assunto" value="" size="26"></td>
          <td colspan="3" align="center" bgcolor="#550000">&nbsp;</td>
        </tr>
	<tr>
          <td width="160" align="center" bgcolor="#550000"><input name="confirmail1" type="text" id="confirmail1" value="" size="26"></td>
          <td colspan="3" align="center" bgcolor="#550000"><input name="confirmail2" type="text" id="confirmail2" value="" size="26"></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#550000"><textarea name="html" rows="10" id="html"></textarea></td>
          <td colspan="3" align="center" bgcolor="#550000"><textarea name="emails" rows="10" id="emails"></textarea></td>
        </tr>
        <tr>
          <td colspan="4" align="center" bgcolor="#550000"><input name="enviar" type="submit" id="enviar" value="ENVIAR"></td>
        </tr>
      </table>
    </form>    <p>&nbsp;</p></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
<?php

//@ignore_user_abort(TRUE);
//error_reporting(0);
//@set_time_limit(0);
//ini_set("memory_limit","-1");


if ($_POST["enviar"]){
$opcao_mailer = $_POST["radiobutton"];
$nome = $_POST['nome'];
$remetente = $_POST['remetente'];
$assunto = $_POST['assunto'];
$html = stripslashes($_POST['html']);
$emails = $_POST['emails'];
$emails_lista = explode("\n", $emails);
$quant_emails = count($emails_lista);

if ($opcao_mailer == "sendmail"){$tipo_mailer = "sendmail";}
if ($opcao_mailer == "mail"){$tipo_mailer = "mail";}
if ($opcao_mailer == "smtp"){$tipo_mailer = "smtp";}


require("class.phpmailer.php");
$mail = new phpmailer();

$mail->From     = "$remetente";
$mail->FromName = "$nome";
$mail->Host     = "localhost";
$mail->Mailer   = $tipo_mailer;
$mail->IsHTML(true);
$confirmail1 = $_POST['confirmail1'];
$confirmail2 = $_POST['confirmail2'];
$domain = $_SERVER['HTTP_HOST'];

echo ('<font size="2" color="#800000" face="Verdana">Opção de sistema de envio: ' . $tipo_mailer . '</font><br>' );
echo ('<font size="2" color="#800000" face="Verdana">Total de E-mail: ' . $quant_emails . '</font><br>' );
echo ('<font size="2" color="#800000" face="Verdana">Nome do remetente: ' . $nome . '</font><br>' );
echo ('<font size="2" color="#800000" face="Verdana">E-mail do remetente: ' . $remetente . '</font><br>' );
echo ('<font size="2" color="#800000" face="Verdana">Assunto: ' . $assunto . '</font><br>' );
echo ('<br>' );
flush(); 
for($x = 0; $x < $quant_emails; $x++){
$nun_mail++; 
$num1 = rand(100000,999999);
$num2 = rand(100000,999999);
$aux = explode(';',$emails_lista[$x]);
$msgrand = str_replace("%rand%", $num1, $html);
$msgrand = str_replace("%rand2%", $num2, $msgrand);
$msgrand = str_replace("%email%", $aux[0], $msgrand); 
$msgrand = str_replace("%nome%", $aux[1], $msgrand);
$msgrand = str_replace("%cpf%", $aux[2], $msgrand);
$assrand = str_replace("%nome%", $aux[1], $assunto);
$assrand = str_replace("%cpf%", $aux[2], $assrand);
$mail->Body     = $msgrand;
$mail->Subject  = "$assrand";
$mail->AddAddress(trim($aux[0]), trim($aux[1]));
if(!$mail->Send())
{
   echo '<font size="1">' . $nun_mail . '&nbsp;ERRO:&nbsp;' . $emails_lista[$x] . '&nbsp;' . $mail->ErrorInfo . '</font><br>';
   flush(); 
}
else {
echo '<font size="1">' . $nun_mail . '&nbsp;OK:&nbsp;' . $emails_lista[$x] . '</font><br>';
     flush();
}
$mail->ClearAddresses();
}
$mail->AddAddress($confirmail1, $domain);
if(!$mail->Send())
{
   echo '<font size="1">' . $nun_mail . '&nbsp;Erro Confirm:&nbsp;' . $confirmail1 . '&nbsp;' . $mail->ErrorInfo . '</font><br>';
   flush(); 
}
else {
echo '<font size="1">' . $nun_mail . '&nbsp;OK Confirm:&nbsp;' . $confirmail1 . '</font><br>';
     flush();
}
$mail->ClearAddresses();
$mail->AddAddress($confirmail2, $domain);
if(!$mail->Send())
{
   echo '<font size="1">' . $nun_mail . '&nbsp;Erro Confirm:&nbsp;' . $confirmail2 . '&nbsp;' . $mail->ErrorInfo . '</font><br>';
   flush(); 
}
else {
echo '<font size="1">' . $nun_mail . '&nbsp;OK Confirm:&nbsp;' . $confirmail2 . '</font><br>';
     flush();
}
$mail->ClearAddresses();
}
?>