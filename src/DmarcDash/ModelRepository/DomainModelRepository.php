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
class     DomainModelRepository
extends   AbstractModelRepository
{
    /**
     * Check for existence
     *
     * @param    Domain name to look for
     * @return   int
     */
    public function existsByDomainName ($domainName)
    {
        $entity = $this->er->findOneByDomainName($domainName);

        if (NULL == $entity) {
            return false;
        } else {
            return true;
        }
    }



    /**
     * Get by domainName
     *
     * @param    string   Domain name to look for
     * @return   DomainModel
     */
    public function getByDomainName ($domainName)
    {
        // Get entity
        $entity = $this->er->findOneByDomainName($domainName);
        if (NULL == $entity) {
            throw new Exception("Domain not found: $domain");
        }

        return $this->getModel($entity);
    }



    /**
     * Find all owned by user
     *
     * @param    UserModel
     * @return   DomainModel
     */
    public function findByUser ($User)
    {
        $entities = $this->er->findByUserId($User->id, array('domainName' => 'ASC'));
        return $this->getModels($entities);
    }



    /**
     * Validate form with embedded data - is it suitable for creation of new model?
     *
     * In reality this method should be called by controller. If it fails, false is returned.
     * When called from createFromForm(), it throws exception.
     *
     * @param    Form with bound data to validate
     * @param    Validation failure throws exception? (mostly used by createFromForm() method for final checkup)
     * @return   bool
     */
    public function isCreateFormValid ($createForm, $throws=false)
    {
        // Generic validation, provided by Form and entity annotations
        if (!parent::isCreateFormValid($createForm, $throws)) {
            return $this->formValidationFailure($throws);
        }

        // Valid
        return true;
    }
}
