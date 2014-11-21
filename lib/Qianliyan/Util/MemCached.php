<?php
/**
 * Qianliyan Util
 *
 * @category   Qianliyan
 * @package    Qianliyan_Util
 * @version    $Id$
 */

class MemCached
{
    private $mem;
    
    private function memConnect(){
        $this->mem = new Memcache;
        $this->mem->connect(__MEMCACHE_IP,__MEMCACHE_PORT) or $this->errordie('memcached connect error'); 
	}

	public function set($ser_key,$values,$flag='',$expire=''){
		$this->memConnect();
		if($this->mem->set($ser_key,$values,$flag,$expire)) 
		    return true;
		else 
		    return false;
	}

	public function get($ser_key){
		$this->memConnect();
		if($var=$this->mem->get($ser_key)) 
		    return $var;
		else 
		    return false;
	}
	
	public function delete($ser_key){
		$this->memConnect();
		if($this->mem->delete($ser_key, 0)) 
		    return true;
		else 
		    return false;
	}
	
	private function errordie($errmsg){
		die($errmsg);
	}
} 