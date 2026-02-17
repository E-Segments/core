<?php

declare(strict_types=1);

namespace Esegments\Core\Results;

use RuntimeException;

/**
 * Exception thrown when attempting to get the value of a None Option.
 */
final class OptionException extends RuntimeException {}
