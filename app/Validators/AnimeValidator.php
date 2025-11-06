<?php
namespace App\Validators;

class AnimeValidator {
    public static function validate($data) {
        $errors = [];

        // Title required, max 255 chars
        if (empty($data['title']) || strlen($data['title']) > 255) {
            $errors[] = 'Title is required (max 255 characters)';
        }

        // Episodes must be positive integer
        if (!empty($data['episodes']) && (!is_numeric($data['episodes']) || $data['episodes'] < 0)) {
            $errors[] = 'Episodes must be a positive number';
        }

        // Rating must be between 0 and 10
        if (!empty($data['rating']) && ($data['rating'] < 0 || $data['rating'] > 10)) {
            $errors[] = 'Rating must be between 0 and 10';
        }

        // Status must be valid enum
        $validStatuses = ['Ongoing', 'Completed', 'Upcoming'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
            $errors[] = 'Invalid status';
        }

        return $errors;
    }
}
