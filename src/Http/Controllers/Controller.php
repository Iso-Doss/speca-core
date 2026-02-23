<?php

namespace Speca\SpecaCore\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Default api error message.
     */
    const API_DEFAULT_ERROR_MESSAGE = 'Oups !!! Un ou plusieurs champ(s) sont incorrect(s)';
}
