<?php

function sanitizeInput($data)
{
    if (!is_array($data)) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    foreach ($data as $key => $value) {
        // Skip password sanitization
        if ($key === 'password') {
            continue;
        }
        $data[$key] = htmlspecialchars(strip_tags(trim($value)));
    }
    return $data;
}
