<?php

namespace App;

trait ApiResponser
{
    protected function success($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
}
