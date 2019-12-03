<?php


namespace App\Service\Invitation;

/**
 * An exception thrown when trying to invite a member that has already been invited.
 */
class InvitationAlreadySentException extends \RuntimeException
{

}
