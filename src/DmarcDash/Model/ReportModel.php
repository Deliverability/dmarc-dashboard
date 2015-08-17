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
class     ReportModel
extends   AbstractModel
{



    /*
     * Get related object
     *
     * @return    Model\*
     */
    public function getDomain ()
    {
        $Repo = $this->getRepository()->getModelRepository('Domain');
        return $Repo->getById($this->domainId);
    }



    /*
     * Count number of records
     *
     * @return    Model\*
     */
    public function getRecordCount ()
    {
        $Repo = $this->getMyModelRepository()->getModelRepository('ReportRecord');
        return count($Repo->findByReportId($this->id));
    }
}
