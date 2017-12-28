<?php


namespace ReconciliationTool;


use ReconciliationTool\ReconciliationAdapter\ReconciliationAdapterInterface;

class ReconciliationProcessor
{
    /**
     * @var ReconciliationAdapterInterface[]
     */
    private $adapters = [];
    
    private $startDate = '';
    
    private $endDate = '';
    
    /**
     * @var CompareStorage
     */
    private $storage;
    
    private $rawDiff = [];
    
    public function addAdapter (ReconciliationAdapterInterface $adapter) 
    {
        $this->adapters[] = $adapter;
    }
    
    /**
     * @param string $startDate
     */
    public function setStartDate(string $startDate)
    {
        $this->startDate = $startDate;
    }
    
    /**
     * @param string $endDate
     */
    public function setEndDate(string $endDate)
    {
        $this->endDate = $endDate;
    }
    
    public function process () 
    {
        $this->prepareStorage();
        $this->writeAdaptersData();
        $this->calcDifference();
    }
    
    public function prepareStorage () 
    {
        $this->storage->setCompareName('compare-'.date('Ymd-His'));
        $this->storage->prepareTable();
    }
    
    public function writeAdaptersData () 
    {
        // query data and write to compare
        foreach ($this->adapters as $adapter) {
            $hashes = $adapter->searchByDateRange($this->startDate, $this->endDate);
            $this->writeHashes($adapter->getAdapterId(), $hashes);
        }
    }
    
    public function writeHashes ($adapterId, $hashes) 
    {
        $binds = [];
        foreach ($hashes as $id => $hash) {
            $binds[] = [
                CompareStorage::F_ADAPTER_ID => $adapterId,
                CompareStorage::F_ITEM_ID    => $id,
                CompareStorage::F_ITEM_HASH  => $hash,
            ];
        }
        
        $this->storage->write($binds);
    }
    
    public function calcDifference () 
    {
        $this->rawDiff = $this->storage->getDifferences($this->adapters[0]->getAdapterId(), $this->adapters[1]->getAdapterId());
    }
    
    /**
     * @param CompareStorage $storage
     */
    public function setStorage(CompareStorage $storage)
    {
        $this->storage = $storage;
    }
    
    /**
     * @return array
     */
    public function getRawDiff()
    {
        return $this->rawDiff;
    }
}