<?php 

//////////////////////////////////////////////////////////////////////////////////////////////////
// https://github.com/PHPJasper/phpjasper Es la libreria usada para integrar con jasper reports/// 
// Libreria: https://github.com/gumiel/libreria-jasper-report-for-php                          ///
// Esta libreria aumenta nuevas caracteristicas a la libreria principal jasper                 ///
//////////////////////////////////////////////////////////////////////////////////////////////////

require __DIR__ . '/phpjasper/src/PHPJasper.php';
use PHPJasper\PHPJasper;


// $formats = [
//         'pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods',
//         'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'
//     ];

class PhpJasperReport
{   
    public $dir;
    public $routeJrxml;
    public $output;
    public $dbConnection;

    function __construct($dbConnection=array(), $dirJrxml='', $output, $formats=array())
    {
        $this->dir          = __DIR__;
        $this->routeJrxml   = $this->dir.$dirJrxml;
        $this->output       = $output;
        $this->dbConnection = $dbConnection;        
        $this->formats      = $formats;
    }

    /**
     * Compila el JRXML y devuelve la ruta del reporte o reportes enviados      
     * @param  [string] $nameJrxml [description]
     * @param  array  $params    [parametros que se enviaran al reporte para procesarlos Ejm. ID]
     * @param  array  $formats   [formatos que generaran el jasper report Ejm. pdf,xls, ... etc  ]
     * @return [string]          [retorna el nombre del archivo con la ruta localizando los reportes]
     */
    function run($nameJrxml, $params=array(), $formats=array())
    {
        $output = '';
        $onlyOneExtencion = '';
        $this->formats = ( empty($formats) )? $this->formats: $formats;
        
        if( $this->verifyData())
        {

            $input  = $this->routeJrxml.$nameJrxml;   

            $jasper = new PHPJasper;
        
            $jasper->compile($input)->execute();

            $data = explode('.', $nameJrxml);


            $input = $this->routeJrxml.$data[0].'.jasper';  
            $output = $this->dir.$this->output.'/report_'.date('Ymdhis').'_'.uniqid();    


            $options = [
                'format' => $this->formats,
                'locale' => 'en',
                'params' => $params,
                'db_connection' => $this->dbConnection
            ];

            $jasper = new PHPJasper;

            $jasper->process(
                $input,
                $output,
                $options
            )->execute();

            if( sizeof($this->formats)==1)
            {
                $onlyOneExtencion = '.'.$this->formats[0];
            }

        }
        
        return $output.$onlyOneExtencion;

    }

    private function verifyData()
    {
        $res = true;
        if( !is_array($this->dbConnection) && empty($this->dbConnection) )
        {
            echo "No tiene coneccion a la BD. ";
            $res = false;
        }

        if( ! is_dir($this->routeJrxml) && $this->routeJrxml=='' )
        {
            echo "No tiene la ruta correcta del jrxml. ";
            $res = false;
        }

        if( !is_array($this->formats) && empty($this->formats) )
        {
            echo "No tiene formatos. ";
            $res = false;
        }
        // echo "string";
        // exit;
        return $res;
    }

    public function forceDowload($file, $name)
    {

        $filename = $name; // el nombre con el que se descargara, puede ser diferente al original 
        // header('Content-type: application/pdf');
        header("Content-type: application/octet-stream");
        header("Content-Type: application/force-download"); 
        header('Content-Disposition: attachment; filename="'.$filename.'"'); 
        readfile($file);
    }
}

 ?>