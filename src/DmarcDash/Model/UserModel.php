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
class     UserModel
extends   AbstractModel
{



    /**
     * Getter - get domain name
     *
     * @return   string
     */
    public function getDomains ()
    {
        return $this->getMyModelRepository()->getModelRepository('Domain')->findByUser($this);
    }



    /**
     * Getter - get domain name
     *
     * @return   string
     */
    public function getReports ()
    {
        return $this->getMyModelRepository()->getModelRepository('Report')->getByUser($this);
    }
}
