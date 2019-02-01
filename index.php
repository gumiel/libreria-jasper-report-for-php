<?php include("PhpJasperReport.php"); ?>

<?php 

$dbConnection = array('driver' => 'postgres', 
                'username' => 'postgres',
                'password' => 'postgres',
                'host' => 'localhost',
                'database' => 'phpjasperreport',
                'port' => '5432');

$jasper = new PhpJasperReport($dbConnection, '/jrxml_store/', '/reports');

$file = $jasper->run('prueba1.jrxml', ['id'=>221],['pdf']);

echo $file;

$jasper->forceDowload($file, 'nuevoOtro.pdf');

 ?>