<?php 


class Log{

    public function logs($mensaje){
		
        $logFile = fopen("/home/mascotasalfalfa/domains/crm.mascotasalfalfa.com/log_pedido_crm.txt", 'a') or die("Error creando archivo");
        fwrite($logFile, "\n".date("d/m/Y H:i:s")." {$mensaje}") or die("Error escribiendo en el archivo");
        fclose($logFile);
    }

}
