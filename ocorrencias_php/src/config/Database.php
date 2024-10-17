<?php

class Sql {

	const HOSTNAME = "localhost";
	const USERNAME = "root";

	const PASSWORD = "root";
	const DBNAME = "ocorrencias";

	private $conn;
	
	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";port:3306;host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD,
			array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false
			)
			
		);

		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	}

	public function setParams($statement, $parameters = array())
	{
		foreach ($parameters as $key => $value) {		
			$this->bindParam($statement, $key, $value);
		}
	}

	public static function bindParam($statement, $key, $value)
	{
		$statement->bindParam($key, $value);	
	}

	public function query($rawQuery, $params = array()):array
	{
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);

		try {
			$stmt->execute();
			return [
				"sucesso" => "ok",
				"erro"    => ""
			];

		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function select($rawQuery, $params = array()):array
	{
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);

		try{
			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			return array(
				"erro" => "Erro:\n{$e->getMessage()}"
			);
		}
	}

}
?>
