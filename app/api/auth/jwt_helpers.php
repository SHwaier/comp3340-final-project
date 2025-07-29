<?php
/**
 * encodes data using base64url encoding.
 * This is a URL-safe version of base64 encoding, which replaces '+' with '-', '/' with '_', and removes padding '=' characters.
 * @param mixed $data data to be encoded.
 * @return string returns the base64url encoded string.
 */
function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * decodes base64url encoded data.
 * This function reverses the base64url encoding process, replacing '-' with '+', '_' with '/', and adding padding '=' characters.
 * @param string $data base64url encoded data to be decoded.
 * @return string returns the decoded data.
 */
function base64url_decode($data)
{
    return base64_decode(strtr($data, '-_', '+/'));
}
