<?php

namespace Starkerxp\DatabaseChecker\Repository;


class MysqlRepository
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * MysqlRepository constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param $database
     *
     * @return array
     */
    public function getTablesStructure($database)
    {
        $sth = $this->pdo->prepare('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=:database GROUP BY TABLE_NAME ORDER BY TABLE_NAME');
        $sth->bindParam(':database', $database);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return array_column($results, 'TABLE_NAME');
    }

    /**
     * @param $database
     * @param $table
     *
     * @return array
     */
    public function fetchIndexStructure($database, $table)
    {
        $sql = 'SELECT 
                  INDEX_NAME,
                  GROUP_CONCAT(COLUMN_NAME) as COLUMN_NAME,
                  NON_UNIQUE
                FROM 
                  information_schema.statistics 
                WHERE 
                  TABLE_SCHEMA= :database AND 
                  TABLE_NAME= :myTable  
                GROUP BY
                  TABLE_NAME,INDEX_NAME
                ORDER BY
                  TABLE_NAME,INDEX_NAME,COLUMN_NAME';
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':database', $database);
        $sth->bindParam(':myTable', $table);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $database
     * @param $table
     *
     * @return array
     */
    public function fetchColumnsStructure($database, $table)
    {
        $sql = 'SELECT 
                  COLUMN_NAME, 
                  DATA_TYPE, 
                  COLUMN_TYPE, 
                  IS_NULLABLE, 
                  COLUMN_DEFAULT, 
                  EXTRA  
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE 
                  TABLE_SCHEMA= :database AND 
                  TABLE_NAME= :myTable 
                ORDER BY ORDINAL_POSITION';
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':database', $database);
        $sth->bindParam(':myTable', $table);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

}
