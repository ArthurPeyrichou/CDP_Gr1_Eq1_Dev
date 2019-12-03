<?php


namespace App\Service\Invitation;

/**
 * An exception thrown when trying to invite a member that is already a collaborator of the project.
 */
class MemberAlreadyExistsException extends \RuntimeException
{

}
