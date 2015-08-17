<?php



/**
 * Namespace definition
 */
namespace DmarcDash\ModelRepository;



/**
 * Namespace imports
 */
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Teon\Symfony\Validation\Exception as ValidationException;



/**
 * Model repository
 */
class     UserModelRepository
extends   AbstractModelRepository
{



    /**
     * Import this convenience trait
     */
    use \Teon\Symfony\ModelRepository\UserModelRepositoryTrait;
}
