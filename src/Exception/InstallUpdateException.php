<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Exception;

use RuntimeException;

class InstallUpdateException extends RuntimeException
{

    protected $message = 'Updating the installation failed';
}
