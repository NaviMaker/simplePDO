<?php
defined('_CN_')||(die('Direct Access Forbidden!'));

class db{
	/*** Database credentials ***/
	public $DB_SERVER	= '127.0.0.1'; //localhost is sometimes really slow
	public $DB_USER 	= 'root';
	public $DB_PASSWORD = '';
	public $DB_NAME 	= '';
	
	/*** Activate debug functions ***/
	public $debug = false;
	
	/*** Keep track of all queries ***/
	public $querynumber = 0;
	
	/*** Connection state ***/
	private $cs = false;
	
	/*** Query parameters ***/
	private $queryparameters = array();
	
	private $pdo;
	private $query;
	
	public function connect()
	{
		try 
		{
			$this->pdo = new PDO('mysql:host='.$this->DB_SERVER.';dbname='.$this->DB_NAME, $this->DB_USER, $this->DB_PASSWORD);
			if( $this->debug ){ $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }
			
			$this->cs = true;
		}
		catch(PDOException $er) 
		{
			throw new Exception ($er->getMessage());
		}
	}
	
	public function disconnect()
	{
		/*** PDO to null to close the connection ***/
	 	$this->pdo = null;
		$this->cs = false;
	}
		
	public function query($q, $fetchmode = PDO::FETCH_ASSOC)
	{
		/*** Open a new connection if needed ***/
		if(!$this->cs) { $this->connect(); }
		
		$q = trim($q);
		
		try
		{
			/*** Prepare the query ***/
			$this->query = $this->pdo->prepare($q);
			$this->query->execute($this->queryparameters);
			
			$this->querynumber++;
		}
		catch(PDOException $e)
		{
			throw new Exception ($er->getMessage());
		}
		
		/*** Find out query type ***/
		$type = strtoupper(substr($q, 0 , 6));
		
		if ($type === 'SELECT') 
		{
			return $this->query->fetchAll($fetchmode);
		}
		elseif($type === 'INSERT' ||  $type === 'UPDATE' || $type === 'DELETE') 
		{
			return $this->query->rowCount();	
		}	
		else
		{
			return NULL;
		}
	}
	
	public function lastInsertId() {
		return $this->query->lastInsertId();
	}
	
	public function setParm($placeholder, $value)
	{
		$this->queryparameters[$placeholder] = $value;
	}
}
?>