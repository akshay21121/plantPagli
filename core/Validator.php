<?php
declare(strict_types=1);

namespace Core;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    public static function make(array $data, array $rules): self {
        return new self($data, $rules);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !$this->passes();
    }

    public function errors(): array {
        return $this->errors;
    }

    public function first(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    private function validate(): void {
        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $value, $ruleName, $params);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void {
        $readable = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$readable} is required.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$readable} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int) ($params[0] ?? 0);
                if (!empty($value)) {
                    if (is_numeric($value) && (float)$value < $min) {
                        $this->addError($field, "{$readable} must be at least {$min}.");
                    } elseif (is_string($value) && mb_strlen($value) < $min) {
                        $this->addError($field, "{$readable} must be at least {$min} characters.");
                    }
                }
                break;

            case 'max':
                $max = (int) ($params[0] ?? 255);
                if (!empty($value)) {
                    if (is_numeric($value) && (float)$value > $max) {
                        $this->addError($field, "{$readable} cannot exceed {$max}.");
                    } elseif (is_string($value) && mb_strlen($value) > $max) {
                        $this->addError($field, "{$readable} cannot exceed {$max} characters.");
                    }
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "{$readable} must be a number.");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "{$readable} contains an invalid selection.");
                }
                break;

            case 'match':
            case 'confirmed':
                $target = $params[0] ?? ($field . '_confirmation');
                $targetVal = $this->data[$target] ?? null;
                if ($value !== $targetVal) {
                    $this->addError($field, "{$readable} does not match.");
                }
                break;

            case 'phone':
                // Clean phone check for 10-digit Indian numbers or with +91
                if (!empty($value)) {
                    $clean = preg_replace('/[^\d]/', '', (string)$value);
                    if (strlen($clean) < 10 || strlen($clean) > 13) {
                        $this->addError($field, "{$readable} must be a valid mobile phone number.");
                    }
                }
                break;

            case 'pincode':
                // 6 digits PIN code
                if (!empty($value)) {
                    if (!preg_match('/^[1-9][0-9]{5}$/', (string)$value)) {
                        $this->addError($field, "{$readable} must be a valid 6-digit Indian PIN code.");
                    }
                }
                break;

            case 'unique':
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;
                if (!empty($value) && !empty($table)) {
                    $db = Database::getInstance();
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
                    $args = [$value];
                    if ($exceptId !== null) {
                        $sql .= " AND `id` != ?";
                        $args[] = $exceptId;
                    }
                    $count = (int) $db->fetchColumn($sql, $args);
                    if ($count > 0) {
                        $this->addError($field, "This {$readable} is already taken.");
                    }
                }
                break;
        }
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }
}
