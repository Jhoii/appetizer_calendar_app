<?php

function res(string $message = '', $data = null, int $code = 200) {
    return response()->json([
        'code' => $code,
        'message' => $message,
        'data' => $data,
    ]);
}
