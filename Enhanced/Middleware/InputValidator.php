<?php
/**
 * Input Validator Middleware
 * Performs thorough server-side checks on user-supplied form data
 */
class InputValidator {
    private $errors = [];

    public function required($field, $value, $label = null) {
        $label = $label ?: $field;
        if (empty(trim($value))) {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    public function email($field, $value) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Please enter a valid email address.";
        }
        return $this;
    }

    public function minLength($field, $value, $min, $label = null) {
        $label = $label ?: $field;
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field] = "$label must be at least $min characters.";
        }
        return $this;
    }

    public function maxLength($field, $value, $max, $label = null) {
        $label = $label ?: $field;
        if (!empty($value) && strlen($value) > $max) {
            $this->errors[$field] = "$label cannot exceed $max characters.";
        }
        return $this;
    }

    public function username($field, $value) {
        if (!empty($value) && !preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
            $this->errors[$field] = "Username can only contain letters, numbers, and underscores.";
        }
        return $this;
    }

    public function passwordStrength($field, $value) {
        if (strlen($value) < PASSWORD_MIN_LENGTH) {
            $this->errors[$field] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters.";
            return $this;
        }
        if (PASSWORD_REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $value)) {
            $this->errors[$field] = "Password must contain at least one uppercase letter.";
            return $this;
        }
        if (PASSWORD_REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $value)) {
            $this->errors[$field] = "Password must contain at least one lowercase letter.";
            return $this;
        }
        if (PASSWORD_REQUIRE_NUMBER && !preg_match('/[0-9]/', $value)) {
            $this->errors[$field] = "Password must contain at least one number.";
            return $this;
        }
        if (PASSWORD_REQUIRE_SPECIAL && !preg_match('/[!@#$%^&*(),.?\":{}|<>]/', $value)) {
            $this->errors[$field] = "Password must contain at least one special character.";
            return $this;
        }
        return $this;
    }

    public function matches($field, $value1, $value2, $label = null) {
        $label = $label ?: $field;
        if ($value1 !== $value2) {
            $this->errors[$field] = "$label do not match.";
        }
        return $this;
    }

    public function numeric($field, $value, $label = null) {
        $label = $label ?: $field;
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = "$label must be a number.";
        }
        return $this;
    }

    public function positive($field, $value, $label = null) {
        $label = $label ?: $field;
        if (!empty($value) && (float)$value < 0) {
            $this->errors[$field] = "$label must be a positive number.";
        }
        return $this;
    }

    public function integer($field, $value, $label = null) {
        $label = $label ?: $field;
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = "$label must be an integer.";
        }
        return $this;
    }

    public function passes() {
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}
?>
