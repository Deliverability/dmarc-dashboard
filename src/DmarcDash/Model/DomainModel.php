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
class     DomainModel
extends   AbstractModel
{



    /**
     * Getter - get domain name
     *
     * @return   string
     */
    public function getUser ()
    {
        return $this->getMyModelRepository()->getModelRepository('User')->getById($this->entity->getUserId());
    }



    /**
     * Getter - get domain name
     *
     * @return   string
     */
    public function isOwnedBy ($User)
    {
        $Owner = $this->getUser();
        if ($Owner->id == $User->id) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * Getter - get all secret tokens for this domain - domain-specific and user-wide
     *
     * @return   string
     */
    public function getAllSecretTokens ()
    {
        $retTokens = array();

        // Get domain-specifc
        $tokens = $this->getRepository()->getEntityManager()->getRepository("DmarcDash:DomainSecretToken")->findByDomainId($this->entity->getId());
        foreach ($tokens as $token) {
            $retTokens[$token->getPubId()] = $token->getSecretToken();
        }

        // Get user-specific
        $tokens = $this->getRepository()->getEntityManager()->getRepository("DmarcDash:UserSecretToken")->findByUserId($this->entity->getUserId());
        foreach ($tokens as $token) {
            $retTokens[$token->getPubId()] = $token->getSecretToken();
        }

        return $retTokens;
    }
}
