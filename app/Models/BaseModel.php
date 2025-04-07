<?php

namespace ExpertsCrm\Models;

defined( 'ABSPATH' ) || exit;

class BaseModel {
    const NOT_FOUND_MSG = "not_found";
    const SERVER_ERROR_MSG = "server_error";
    const INVALID_DATA_MSG = "invalid_data";
    const NOT_FOUND_CODE = 404;
    const SERVER_ERROR_CODE = 500;
    const INVALID_DATA_CODE = 400;
}