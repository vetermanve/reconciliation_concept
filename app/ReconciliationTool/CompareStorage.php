<?php

namespace ReconciliationTool;

class CompareStorage
{
    const F_ADAPTER_ID = 'adapter_id';
    const F_ITEM_ID    = 'item_id';
    const F_ITEM_HASH  = 'item_hash';
    
    /**
     * @var \PDO
     */
    private $pdo;
    
    private $compareName = 'default';
    private $tableName = '';
    
    private $dsn;
    private $user;
    private $password;
    
    /**
     * CompareStorage constructor.
     *
     * @param $dsn
     * @param $user
     * @param $password
     */
    public function __construct($dsn, $user, $password)
    {
        $this->dsn      = $dsn;
        $this->user     = $user;
        $this->password = $password;
    }
    
    /**
     * @return \PDO
     */
    public function getConnection () 
    {
        if (!$this->pdo) {
            $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
        }
        
        return $this->pdo;
    }
    
    public function setCompareName($string)
    {
        $this->compareName = $string;   
    }
    
    public function prepareTable () 
    {
        $table = preg_replace('/[\W]+/', '_', 'cmp_'.$this->compareName);
        $this->tableName = $table;
        
        $connection = $this->getConnection(); 
        $connection->query("drop table IF EXISTS ". $table)->fetch();
        
        $createTableSql = $this->getCreateTableSql($table);
        $connection->query($createTableSql)->fetch();
    
        $createInexSql = $this->getCreateIndexSql($table);
        $connection->query($createInexSql)->fetch();
    }
    
    /**
     * @param $sql
     *
     * @return \PDOStatement
     */
    public function query ($sql) 
    {
        return $this->pdo->query($sql);
    }
    
    private function getCreateTableSql ($tableName) {
        $ddl = "
        create table $tableName 
        (
            adapter_id smallint,
            item_id varchar(255),
            item_hash varchar(255)
        )";
    
        return $ddl;
    }
    
    public function getCreateIndexSql ($tableName) 
    {
        $sql = "create unique index {$tableName}_index on $tableName (adapter_id, item_id, item_hash)";
        return $sql;
    }
    
    /**
     *  
     */
    public function write($binds)
    {
        $bindStructure = [
            self::F_ADAPTER_ID,
            self::F_ITEM_ID,
            self::F_ITEM_HASH,
        ];
        $keysString = implode(',' ,$bindStructure);
        
        $values = [];
        foreach ($binds as $row) {
            $values []= "({$row[self::F_ADAPTER_ID]}, '{$row[self::F_ITEM_ID]}', '{$row[self::F_ITEM_HASH]}')";
        }
        
        $valuesString = implode(',', $values);
    
        $this->getConnection()->query("INSERT INTO public.{$this->tableName} ({$keysString}) VALUES {$valuesString}")->fetch();
    }
    
    public function getDifferences ($type1, $type2, $limit = 1000, $offset = 0) 
    {
        
        $query = 
"WITH 
    cp1 AS (SELECT * FROM {$this->tableName} WHERE {$this->tableName}.adapter_id = {$type1}),
    cp2 AS (SELECT * FROM {$this->tableName} WHERE {$this->tableName}.adapter_id = {$type2})
    SELECT 
        cp1.adapter_id cp1a, 
        cp1.item_id    cp1id, 
        cp1.item_hash  cp1h, 
        cp2.adapter_id cp2a, 
        cp2.item_id    cp2id, 
        cp2.item_hash  cp2h 
    FROM cp1 
    FULL OUTER JOIN cp2 ON
        cp1.item_id = cp2.item_id
        AND cp1.item_hash = cp2.item_hash
    WHERE (cp1.adapter_id ISNULL OR cp2.adapter_id ISNULL) LIMIT $limit OFFSET $offset;
";
        
        return $this->getConnection()->query($query, \PDO::FETCH_NAMED)->fetchAll();
    }
}