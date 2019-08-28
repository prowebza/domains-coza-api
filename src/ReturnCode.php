<?php

namespace Balfour\DomainsResellerAPI;

abstract class ReturnCode
{
    public const NOT_AVAILABLE_OR_FAILED = 0;
    public const SUCCESSFUL = 1;
    public const PENDING_ACTION_SUCCESSFUL = 2;
    public const ITEM_NOT_PENDING_ACTION = 4;
    public const INVALID_CREDENTIALS = 6;
    public const AUTHENTICATION_ERROR = 7;
    public const ASSOCIATION_PROHIBITS_OPERATION = 8;
    public const UNKNOWN_ERROR = 9;
    public const MISSING_PARAMETER = 10;
    public const ITEM_DOES_NOT_EXIST = 11;
    public const STATUS_PROHIBITS_OPERATION = 12;
    public const ITEM_ALREADY_EXISTS = 13;
    public const COMMAND_SYNTAX_ERROR = 14;
    public const UNKNOWN_COMMAND = 15;
    public const DATABASE_CALL_FAILED = 16;
    public const INTERNAL_ERROR = 17;
    public const CONNECTION_REFUSED = 18;
    public const BILLING_FAILURE = 19;
    public const REQUEST_TIMED_OUT = 20;
    public const CONNECTION_FAILED_TO_PROVIDER = 22;
}
