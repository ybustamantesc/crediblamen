<?php
$hostname='localhost';$username='root';$password='';$database='minitas';
$mysqli=new mysqli($hostname,$username,$password,$database);
if($mysqli->connect_errno){echo "connect error";exit(1);} $mysqli->set_charset('utf8');
$sql="SELECT pr.*, s.idcliente FROM tb_prestamos pr JOIN tb_solicitudes s ON s.idsolicitud=pr.idsolicitud WHERE DATE(pr.created_at) BETWEEN '2026-01-01' AND '2026-01-26' AND s.idcliente='3'";
$res=$mysqli->query($sql);
if(!$res){echo json_encode(['error'=>true,'mysql'=>$mysqli->error,'query'=>$sql]);exit(1);} $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r; echo json_encode(['count'=>count($rows),'rows'=>$rows],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
