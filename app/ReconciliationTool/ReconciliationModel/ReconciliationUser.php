<?php

namespace ReconciliationTool\ReconciliationModel;

use ReconciliationTool\ReconciliationConfig;

class ReconciliationUser implements ReconciliationModelInterface
{
    private $id;
    private $name;
    private $phone;
    private $sex;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getHash()
    {
        return md5(json_encode(get_object_vars($this)));
    }
    
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    
    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }
    
    public function getModelId()
    {
        return ReconciliationConfig::MODEL_USER;
    }
}