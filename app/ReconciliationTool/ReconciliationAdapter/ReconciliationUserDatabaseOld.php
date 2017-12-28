<?php

namespace ReconciliationTool\ReconciliationAdapter;


use ReconciliationTool\ReconciliationConfig;
use ReconciliationTool\ReconciliationModel\ReconciliationUser;

class ReconciliationUserDatabaseOld implements ReconciliationAdapterInterface
{
    public function getAdapterId()
    {
        return ReconciliationConfig::ADAPTER_USER_DB_OLD;
    }
    
    public function searchByDateRange($startDate, $endDate)
    {
        return $this->_getHashMap();
    }
    
    public function getByIds($ids)
    {
        $data = $this->_getData();
        return array_intersect_key($data, array_flip($ids));
    }
    
    public function getApplyModelId()
    {
        return ReconciliationConfig::MODEL_USER;
    }
    
    private function _getHashMap()
    {
        $data = $this->_getData();
        
        $hashMap = [];
        foreach ($data as $id => $user) {
            $recModel = new ReconciliationUser();
            $recModel->setName($user['fullname']);
            $recModel->setSex($user['sex']);
            $recModel->setPhone($user['telephone']);
            $recModel->setId($user['id']);
            
            $hashMap[$recModel->getId()] = $recModel->getHash();
        }
        
        return $hashMap;
    }
    
    private function _getData()
    {
        $data = [];
        
        foreach (range(2,101) as $id) { // range is shifted
            $user = [
                'id' => $id,
                'fullname' => 'User '.$id,
                'sex' => $id%2,
                'telephone' => '99000'.$id
                    .($id%10 === 7 ? '777' : '') /// each 10 users with id end of 7 mistake in phone
                ,
            ];
            
            $data[$id] = $user;
        }
        
        return $data;
    }
    
    
}