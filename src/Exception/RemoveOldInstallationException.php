<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Exception;

use RuntimeException;

class RemoveOldInstallationException extends RuntimeException
{

    protected $message = 'Could not remove old installation directory';
}
