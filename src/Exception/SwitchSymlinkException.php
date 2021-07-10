<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Exception;

use RuntimeException;

class SwitchSymlinkException extends RuntimeException
{

    protected $message = 'Switching the symlink for the new version failed';
}
