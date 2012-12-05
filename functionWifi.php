<?
/*Skript pro odpojení uživatele
copyright Michal Šanda, 2012
*/
        function disconectAll(){

include('mysql_c2.php');
$sql='SELECT * FROM leases';
$result=mysql_query($sql) or die(mysql_error());

    while($row = mysql_fetch_array($result))  {
          /*vytáhneme z DB všechny sloupce*/
$mac=$row['mac'];
exec('/usr/bin/sudo /sbin/iptables -t nat -D PREROUTING -m mac --mac-source '.$mac.' -j NET ');
$sql='DELETE FROM leases WHERE mac=\''.$mac.'\'';
$res=mysql_query($sql) or die(mysql_error());
 }
mysql_close();
return "odpojeno";



}

function loginToSite($nick,$heslo){
include ('mysql_c.php');
/*získáno z formuláře*/
/*$heslo=$_POST['pass'];
$nick=$_POST['nick']; */
/*dotaz na DB - viz. mysql_c.php*/
$result = mysql_query("SELECT * FROM `uzivatele` WHERE `username` = '$nick'");
while($row = mysql_fetch_array( $result ))  {
          /*vytáhneme z DB všechny sloupce*/
$heslo_z_db=$row['password'];
$nickusera=$row['username'];
$id_user=$row['id_user'];

 }

/*ověříme si zadané heslo*/
if(md5($heslo)==$heslo_z_db)
{


/*Samotné přidání do iptables
Musí se dopsat přidávání do DB atp...
*/
$sql='SELECT id_user_service FROM enabled WHERE enabled.id_user=\''.$id_user.'\' LIMIT 1';
$result=mysql_query($sql) or die(mysql_error());
$ok=mysql_fetch_array($result);

if($ok!=""||$ok!=null){

$ip = $_SERVER['REMOTE_ADDR'];                //získáme si IP
  $mac = exec('/usr/sbin/arp -an|grep '.$ip.'|awk \'{print $4}\'');    //arp je fce v systémů, využijeme tedy php_exec
   exec('/usr/bin/sudo /sbin/iptables -t nat -I PREROUTING -m mac --mac-source '.$mac.' -j NET ');  //přidáme uživatele do příslušných natovacích tabulek
   /*získáme si unix tiime*/
  $time=time();
  /*vložíme údaje potřebnék administraci do DB*/
$sql='INSERT INTO `captive_portal`.`leases` (`id_user`, `nick`,`ip`, `mac`, `time`) VALUES (\''.$id_user.'\',\''.$nickusera.'\',\''.$ip.'\',\''.$mac.'\',\''.$time.'\')';
$result=mysql_query($sql) or die (mysql_error()); // vykonáme SQL query
mysql_close();     //ukončíme spojení s databází
  // if($chyba!=""){        /* pokud nám exec něco vrátí, tak se jedná o chybu*/

   /*dočasné řešení, nikdy uživateli nevypisovat systémové chyby!!!*/
  // echo $chyba;
   /*--------------------------------------------------*/
   //}
return "Úspěšně přihlášen";  }else{
return "Komponentu wi-fi máte zakázanou!";
}
/*Přesměrování na předchozí stránku*/
}
else{
return "Špatné heslo!";
}
}
?>
