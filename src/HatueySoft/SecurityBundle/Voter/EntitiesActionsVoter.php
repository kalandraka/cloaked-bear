<?php

namespace HatueySoft\SecurityBundle\Voter;

use HatueySoft\SecurityBundle\Manager\AclRulesManager;
use HatueySoft\SecurityBundle\Utils\VoterAttributesChecker;
use HatueySoft\SecurityBundle\Utils\VoterClassChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * Class EntitiesActionsVoter
 *
 * @package HatueySoft\SecurityBundle\Voter
 */
class EntitiesActionsVoter implements VoterInterface
{
    /**
     * @var AclRulesManager
     */
    private $rulesManager;

    /**
     * @var VoterAttributesChecker
     */
    private $attributesChecker;

    /**
     * @var VoterClassChecker
     */
    private $classChecker;

    /**
     * DefaultEntityActionsVoter constructor.
     *
     * @param AclRulesManager $rulesManager
     * @param VoterAttributesChecker $attributesChecker
     * @param VoterClassChecker $classChecker
     */
    function __construct(
        AclRulesManager $rulesManager,
        VoterAttributesChecker $attributesChecker,
        VoterClassChecker $classChecker)
    {
        $this->rulesManager = $rulesManager;
        $this->attributesChecker = $attributesChecker;
        $this->classChecker = $classChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return $this->attributesChecker->supportsAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->classChecker->supportsClass($class);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass($object)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for this voter'
            );
        }

        // set the attribute to check against
        $attribute = trim(strtolower($attributes[0]));

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object(i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        // check first for role permission if fails check user permission
        $roles = $user->getRoles();
        $realClass = $object;
        if (is_object($object)) {
            $realClass = ClassUtils::getRealClass($object);
        }

        if ($this->rulesManager->checkEntityRolePermission($realClass, $roles, $attribute)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        if ($this->rulesManager->checkEntityUserPermission($realClass, $user->getUsername(), $attribute)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
