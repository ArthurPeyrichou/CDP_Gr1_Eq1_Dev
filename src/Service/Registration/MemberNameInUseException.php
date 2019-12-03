<?php


namespace App\Service\Registration;

/**
 * An exception thrown when trying to create a member with the same name as an existing one.
 */
class MemberNameInUseException extends \RuntimeException
{

}
