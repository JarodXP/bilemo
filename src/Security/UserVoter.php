<?php

declare(strict_types=1);


namespace App\Security;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class UserVoter
 * @package App\Security
 */
class UserVoter extends Voter
{
    public const EDIT = 'edit';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject)
    {
        //Checks if attribute is supported
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        //Checks if subject is supported
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $company = $token->getUser();

        if (!$company instanceof Company) {
            return false;
        }

        $user = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($company, $user);
            default:
                throw new InvalidArgumentException("This attribute doesn't exisit");
        }
    }

    /**
     * Allow company to edit / read user
     * @param Company $company
     * @param User $user
     * @return bool
     */
    private function canEdit(Company $company, User $user)
    {
        return $company === $user->getCompany();
    }
}
