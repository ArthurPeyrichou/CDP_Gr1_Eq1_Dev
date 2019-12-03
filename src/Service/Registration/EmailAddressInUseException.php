<?php


namespace App\Service\Registration;

/**
 * An exception thrown when trying to create a member with the same email address as an existing one.
 */
class EmailAddressInUseException extends \RuntimeException
{

}
