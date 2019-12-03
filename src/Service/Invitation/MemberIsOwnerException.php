<?php


namespace App\Service\Invitation;

/**
 * An exception thrown when trying to invite a member that is also the owner of the projet.
 */
class MemberIsOwnerException extends \RuntimeException
{

}
