<?php
namespace Internal;

/**
 * internal enums
 */
class InternalEnum
{
	// common
	const ERR_COMMON = 0;
	const CONTROLLER_NOT_EXIST = 2404;
	const METHOD_NOT_EXIST = 1404;
	const TPL_NOT_EXIST = 0404;

	// rute enums
	const URL_METHOD_ERR = 1;
	const URL_RULE_ERR = 2;

	// show error
	const ERR_EXCEPTION_TITLE = 'Exception throwed.';
	const ERR_ERROR_TITLE = 'Some errors hapened.';
}