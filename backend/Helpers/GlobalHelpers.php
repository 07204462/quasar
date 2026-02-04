<?php

namespace App\Helpers;

/** This Global Helper Class */
class GlobalHelpers
{
    public function __construct()
    {
    }

    /**
     * Add committed by information to table data
     *
     * @param array $table Reference to table data array
     * @param int $type Type of commit (1=created_by, 2=modified_by, 3=deleted_by)
     * @return void
     */
    public function addCommittedBy(&$table, $type = 0)
    {
        /* Add Modified By  */
        $commit = null;
        if ($type == 1) {
            $commit = 'created_by';
        } elseif ($type == 2) {
            $commit = 'modified_by';
        } elseif ($type == 3) {
            $commit = 'deleted_by';
        } else {
            $commit = 0;
        }
        
        // Add user ID from session if available
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['_active_session']['id'])) {
            $table[$commit] = $_SESSION['_active_session']['id'];
        } else {
            $table[$commit] = null;
        }
    }

    /**
     * Validate required fields in payload
     *
     * @param array $payload The data payload
     * @param array $required_fields Array of required field names
     * @return array|bool Returns true if valid, or array of missing fields
     */
    public function validateRequiredFields(array $payload, array $required_fields)
    {
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!isset($payload[$field]) || empty($payload[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (empty($missing_fields)) {
            return true;
        }
        
        return $missing_fields;
    }

    /**
     * Sanitize input data
     *
     * @param mixed $data The data to sanitize
     * @return mixed Sanitized data
     */
    public function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate random string
     *
     * @param int $length Length of random string
     * @return string Random string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    /**
     * Format date for display
     *
     * @param string $date Date string
     * @param string $format Output format (default: 'F j, Y')
     * @return string Formatted date
     */
    public function formatDate($date, $format = 'F j, Y')
    {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }
        
        return date($format, $timestamp);
    }
}