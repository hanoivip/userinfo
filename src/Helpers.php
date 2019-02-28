<?php

function hideEmail($email)
{
    $length = strlen($email);
    if ($length <= 2)
        return $email;
    return $email[0] . str_repeat('*', $length - 2) . $email[$length - 1];
}

function hidePassword($password)
{
    return '******';
}