<?php



/**
 * Namespace definition
 */
namespace DmarcDash\ModelRepository;



/**
 * Namespace imports
 */
use Teon\Symfony\ModelRepository\AbstractModelRepository as ParentAbstractModelRepository;



/**
 * Abstract model repository definition
 */
abstract class   AbstractModelRepository
extends    ParentAbstractModelRepository
{
    /**
     * For autodeterminating class names of models and entities
     */
    protected $symfonyBundleName           = "DmarcDash";



    /**
     * For autodeterminating class name of model and entity
     */
    protected $modelClassPrefix            = "\DmarcDash\Model\\";
    protected $entityClassPrefix           = "\DmarcDash\Entity\\";
    protected $entityRepositoryClassPrefix = "\DmarcDash\EntityRepository\\";
    protected $createFormClassPrefix       = "\DmarcDash\Form\\";
}
