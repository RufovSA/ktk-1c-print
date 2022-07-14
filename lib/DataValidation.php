<?php

class DataValidation
{
    public static string $message = '';

    /**
     * @param string $snils
     * @param string $error_message
     * @param mixed $error_code
     * @return boolean
     */
    public static function validateSnils($snils, string &$error_message = '', &$error_code = null)
    {
        $result = false;
        $snils = (string)$snils;
        if (!$snils) {
            $error_code = 1;
            $error_message = 'СНИЛС пуст';
        } elseif (preg_match('/[^0-9]/', $snils)) {
            $error_code = 2;
            $error_message = 'СНИЛС может состоять только из цифр';
        } elseif (strlen($snils) !== 11) {
            $error_code = 3;
            $error_message = 'СНИЛС может состоять только из 11 цифр';
        } else {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (int)$snils[$i] * (9 - $i);
            }
            $check_digit = 0;
            if ($sum < 100) {
                $check_digit = $sum;
            } elseif ($sum > 101) {
                $check_digit = $sum % 101;
                if ($check_digit === 100) {
                    $check_digit = 0;
                }
            }
            if ($check_digit === (int)substr($snils, -2)) {
                $result = true;
            } else {
                $error_code = 4;
                $error_message = 'Неправильное контрольное число';
            }
        }
        self::$message = $error_message;
        return $result;
    }
}