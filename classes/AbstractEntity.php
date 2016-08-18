<?php

namespace classes;

use classes\Connection;

abstract class AbstractEntity {

    /**
     * conexão (PDO)
     * @var Connection
     */
    protected $db;

    /**
     * sql executado nos métodos
     * @var string
     */
    private $sql;

    /**
     * caminho completo da tabela (db.schema.table)
     * @var string
     */
    public $table = null;

    /**
     * atributos (chave=valor)
     * @var array 
     */
    public $attributes = array();

    /**
     * atributo id
     * @var int 
     */
    public $id = 0;

    /**
     * retorna o valor do atributo
     * @param string $name
     * @return string
     */
    public function __get($name) {
        return $this->attributes[$name];
    }

    /**
     * define o valor do atributo
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * seta a conexão com o banco
     * quando fornecido o id da tabela seta os atributos
     * @param int $id = id da tabela
     */
    public function __construct($id = null) {
        $this->db = Connection::getConnection();
    }

    public function setId($id) {
        $this->id = $id;
    }

    /**
     * cria e executa a query de select
     * @param array $aWhere = condicões da query
     * @return array
     */
    public function select(array $aWhere) {
        $sSQL = "SELECT * FROM {$this->table} ";
        $i = 1;
        if ($aWhere) {
            $sSQL .= "WHERE ";
            foreach ($aWhere as $sColumn => $sValue) {
                if ($i < sizeof($aWhere)) {
                    $sSQL .= "{$sColumn} = '{$sValue}' AND ";
                } else {
                    $sSQL .= "{$sColumn} = '{$sValue}'";
                }
                $i++;
            }
        }
        $this->sql = $sSQL;
        $sth = $this->db->prepare($this->sql);
        if ($params) {
            $sth->execute($params);
        } else {
            $sth->execute();
        }

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * retorna um registro
     * @param int $id = id da tabela
     * @return array
     */
    public function findOneById($id) {
        $aRet = $this->select(array('id' => $id));
        if ($aRet) {
            return $aRet[0];
        }
        return array();
    }
    
    /**
     * retorna uma única linha de registro
     * @param array $aWhere
     * @return obhect
     */
    public function findOne(array $aWhere) {
        $sSQL = "SELECT * FROM {$this->table} ";
        $i = 1;
        if ($aWhere) {
            $sSQL .= "WHERE ";
            foreach ($aWhere as $sColumn => $sValue) {
                if ($i < sizeof($aWhere)) {
                    $sSQL .= "{$sColumn} = '{$sValue}' AND ";
                } else {
                    $sSQL .= "{$sColumn} = '{$sValue}'";
                }
                $i++;
            }
        }

        $sth = $this->db->prepare($sql);
        if ($params) {
            foreach ($params as $param => $value) {
                $sth->bindValue($param, $value);
            }
        }
        $sth->execute();
        $aData = $sth->fetch(PDO::FETCH_ASSOC);

        return $this;
    }

    /**
     * cria e executa a query de insert baseado no array $aData
     * retorna o id inserido
     * @param string $sTable = nome da tabela
     * @param array $aData = dados a serem inseridos
     * @return int
     */
    public function insert() {
        $sColumns = implode(", ", array_keys($this->attributes));
        $aParams = array();
        $sParams = null;
        $i = 0;
        foreach ($this->attributes as $sColumn => $sValue) {
            $i++;
            $sParams.=":{$sColumn}";
            if (count($this->attributes) > $i) {
                $sParams.=", ";
            }
            $aParams[":{$sColumn}"] = $sValue;
        }
        $sSQL = "INSERT INTO {$this->table} ({$sColumns}) VALUES ({$sParams}); \n";
        $this->sql = $sSQL;
        $this->execute($sSQL, $aParams);
        return $this;
    }


    /**
     * cria e executa a query de insert para mais de um registro
     * @param array $aData
     * @return $this->db
     */
    public function multipleInsert(array $aData) {
        $sColumns = implode(", ", array_keys($aData[0]));
        $sSQL = "INSERT INTO {$this->sTable} ({$sColumns}) VALUES \n ";
        $iTotalRec = count($aData); //total de registro a serem importados
        foreach ($aData as $aRec) {
            $sValues = implode("', '", array_values($aRec));
            if ($i < $iTotalRec) {
                $sSQL.= "({$sValues}), ";
            } else {
                $sSQL.= "({$sValues}); ";
            }
            $i++;
        }
        $this->sql = $sSQL;
        return $this->db->Execute($sSQL);
    }

    /**
     * cria e executa a query de update baseado no array $aData
     * @return $this->db
     */
    public function update() {
        $sSQL = "UPDATE {$this->table} SET ";
        $i = 1;
        $id = isset($this->attributes['id']) ? $this->attributes['id'] : $this->id;
        unset($this->attributes['id']);
        foreach ($this->attributes as $sColumn => $sValue) {
            if ($i < sizeof($this->attributes)) {
                $sSQL.= "{$sColumn} = '{$sValue}', \n";
            } else {
                $sSQL.= "{$sColumn} = '{$sValue}' \n";
            }
            $i++;
        }
        $sSQL .= "WHERE id = {$id}";
        $this->sql = $sSQL;
        return $this->execute($sSQL);
    }

    /**
     * cria e executa a query de delete
     * @param int $iId = id do registro
     */
    public function delete($iId) {
        $sSQL = "DELETE FROM {$this->table} WHERE id = {$iId} ";
        $this->sql = $sSQL;
        return $this->execute($sSQL);
    }

    /**
     * retorna a query criada pelos métodos
     * @return string
     */
    public function getSQL() {
        return $this->sql;
    }
    
    /**
     * transforma um array simples em um atributos da classe
     * @param array $aData
     */
    public function array_to_object($aData) {
        if (is_array($aData)) {
            foreach ($aData as $sKey => $sValue) {
                $this->$sKey = $sValue;
            }
        }
    }    
    
    /**
     * tenta fazer update se possui o id
     * tenta fazer insert se não possui id
     * @return $this
     */
    public function save() {
        if(!isset($this->attributes['id']) && empty($this->id)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }


    public function execute($sql, $params = false) {
        $sql = trim($sql);
        $sth = $this->db->prepare($sql);
        if ($params) {
            foreach ($params as $param => $value) {
                $sth->bindValue($param, $value);
            }
        }
        return $sth->execute();
    }

}
