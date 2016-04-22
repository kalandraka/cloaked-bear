<?php

namespace HatueySoft\SecurityBundle\Utils;

/**
 * Class VoterAttributesChecker
 *
 * @package HatueySoft\SecurityBundle\Utils
 */
class VoterAttributesChecker
{
    const CREATE = 'create';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const LISTS = 'list';
    const SEARCH = 'search';

    /**
     * @param string $attribute
     *
     * @return boolean
     */
    public function supportsAttribute($attribute)
    {
        return in_array(trim(strtolower($attribute)), array(
            self::CREATE,
            self::SHOW,
            self::EDIT,
            self::DELETE,
            self::LISTS,
            self::SEARCH
        ));
    }

    /**
     *
     * @return array
     */
    public function getSupportsAttributes()
    {
        return array(
            self::CREATE,
            self::SHOW,
            self::EDIT,
            self::DELETE,
            self::LISTS,
            self::SEARCH
        );
    }
}
