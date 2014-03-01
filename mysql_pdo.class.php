<?php
defined('_CN_')||(die('Direct Access Forbidden!'));

class db{
	/*** Database credentials ***/
	static $DB_SERVER	= '127.0.0.1'; //localhost is sometimes really slow
	static $DB_USER 	= 'root';
	static $DB_PASSWORD = '';
	static $DB_NAME 	= '';
	
	/*** Activate debug functions ***/
	static $debug = true;
	
	/*** Keep track of all queries ***/
	public $querynumber = 0;
	
	/*** Connection state ***/
	private $cs = false;
	
	/*** Query parameters ***/
	private $queryparameters = array();
	
	public function connect()
	{
		try 
		{
			$pdo = new PDO('mysql:host='.self::$DB_SERVER.';dbname='.self::$DB_NAME, self::$DB_USER, self::$DB_PASSWORD);
			if( self::$debug ){ $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }
			
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
			$query = $this->pdo->prepare($q);
			$query->execute(self::$queryparameters);
			
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
		self::$queryparameters[$placeholder] = $value;
	}
}
?>