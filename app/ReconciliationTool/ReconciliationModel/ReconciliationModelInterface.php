<?php

namespace ReconciliationTool\ReconciliationModel;

interface ReconciliationModelInterface
{
    public function getId ();
    public function getHash ();
    public function getModelId ();
}