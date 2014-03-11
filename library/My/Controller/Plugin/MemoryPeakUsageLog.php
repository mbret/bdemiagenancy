<?php 
/**
 * Register the memory of the operations until shutdown in the logger provided
 */
class My_Controller_Plugin_MemoryPeakUsageLog    extends Zend_Controller_Plugin_Abstract{ 
    
    protected $_log = null;     
    public function __construct(Zend_Log $log)    {        
        $this->_log = $log;    
   }     
   
   public function dispatchLoopShutdown()    {        
        $peakUsage = memory_get_peak_usage(true);        
        $url = $this->getRequest()->getRequestUri();        
        $this->_log->info($peakUsage . ' (bytes), ' . ($peakUsage/1024) . ' (ko), ' . (($peakUsage/1024)/1014) . ' (mo) => ' . $url);    
   } 
            
}
