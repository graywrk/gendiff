<?php

namespace Differ\Reporters\Json;

function report($AST)
{
    return json_encode($AST, JSON_PRETTY_PRINT);
}
