<?php require_once 'config.php';check();$d=$_POST['d']??'';
if(province($d)){$_SESSION['loc']=$d;if(me())q('UPDATE users SET district=? WHERE id=?',[$d,me()['id']]);}else unset($_SESSION['loc']);
$r=$_POST['r']??'';header('Location: '.(preg_match('#^/[^/\\\\]#',$r)?$r:'index.php'));
