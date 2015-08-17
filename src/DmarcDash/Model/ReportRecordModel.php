<?php
/**
 * DmarcDash
 *
 * Copyright (C) 2014+ Teon d.o.o.
 *
 * This program is a private property and shall not be used, nor distributed
 * without explicit written permission from the owner.
 */



/*
 * Namespace definition
 */
namespace DmarcDash\Model;



/*
 * Namespace imports
 */
//use DmarcDash\ModelRepository\Mailbox as RepoBase;



/*
 * Class definition
 */
class     ReportRecordModel
extends   AbstractModel
{



    /*
     * Get related object
     *
     * @return    Model\*
     */
    public function getReport ()
    {
        $Repo = $this->getRepository()->getModelRepository('Report');
        return $Repo->getById($this->reportId);
    }
}
