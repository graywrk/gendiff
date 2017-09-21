<?php

namespace Differ\Reports\Json;

function report($AST)
{
    return json_encode($AST, JSON_PRETTY_PRINT);
}
