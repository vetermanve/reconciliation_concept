<?php

namespace ReconciliationTool\ReconciliationAdapter;

interface ReconciliationAdapterInterface
{
    public function searchByDateRange ($startDate, $endDate);
    public function getByIds ($ids);
    public function getApplyModelId ();
    public function getAdapterId ();
}