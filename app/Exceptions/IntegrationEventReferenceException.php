<?php

namespace App\Exceptions;

use Exception;

/**
 * An integration callback referenced a main-table row that does not exist
 * (or an unknown reference_type), so the event was not stored.
 */
class IntegrationEventReferenceException extends Exception {}
