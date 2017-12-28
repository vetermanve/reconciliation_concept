<?php


namespace ReconciliationTool\ReconciliationAdapter;


use ReconciliationTool\ReconciliationConfig;
use ReconciliationTool\ReconciliationModel\ReconciliationUser;

class ReconciliationUserDatabaseNew implements ReconciliationAdapterInterface
{
    public function getAdapterId()
    {
        return ReconciliationConfig::ADAPTER_USER_DB_NEW;
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
        foreach ($data as $user) {
            $recModel = new ReconciliationUser();
            $recModel->setName($user['firstname']);
            $recModel->setSex($user['gender']);
            $recModel->setPhone($user['mobile']);
            $recModel->setId($user['id']);
            
            $hashMap[$recModel->getId()] = $recModel->getHash();
        }
        
        return $hashMap;
    }
    
    private function _getData()
    {
        $data = [];
        
        foreach (range(1,100) as $id) {
            $user = [
                'id' => $id,
                'firstname' => 'User '.$id,
                'gender' => 
//                    $id%2
                    (($id %10 === 9) ? 3 : $id%2) /// each 10 user with end of 9 random mistake in gender
                ,
                'mobile' => '99000'.$id,
            ];
            
            $data[$id] = $user;
        }
        
        return $data;
    }
    
}