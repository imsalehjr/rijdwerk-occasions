<?php

declare(strict_types=1);

namespace App;

final class Validator
{
    private array $errors = [];
    private array $clean = [];

    public function __construct(private readonly array $input)
    {
    }

    public function text(string $key, string $label, int $min, int $max, bool $required = true, bool $multiline = false): self
    {
        $raw = $this->input[$key] ?? '';
        $value = is_string($raw) ? trim($raw) : '';
        $value = $multiline
            ? (string) preg_replace('/[^\P{C}\n\t]/u', '', str_replace("\r\n", "\n", $value))
            : (string) preg_replace('/\p{C}+/u', ' ', $value);
        $value = trim($value);
        $length = mb_strlen($value);

        if ($value === '' && !$required) {
            $this->clean[$key] = '';
            return $this;
        }
        if ($value === '') {
            $this->errors[$key] = "Vul je {$label} in.";
        } elseif ($length < $min) {
            $this->errors[$key] = ucfirst($label) . " is te kort (minimaal {$min} tekens).";
        } elseif ($length > $max) {
            $this->errors[$key] = ucfirst($label) . " is te lang (maximaal {$max} tekens).";
        }
        $this->clean[$key] = $value;

        return $this;
    }

    public function email(string $key): self
    {
        $raw = $this->input[$key] ?? '';
        $value = is_string($raw) ? mb_strtolower(trim($raw)) : '';
        if ($value === '') {
            $this->errors[$key] = 'Vul je e-mailadres in.';
        } elseif (mb_strlen($value) > 190 || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$key] = 'Dit e-mailadres klopt niet. Controleer het en probeer opnieuw.';
        }
        $this->clean[$key] = $value;

        return $this;
    }

    public function phone(string $key, bool $required): self
    {
        $raw = $this->input[$key] ?? '';
        $value = is_string($raw) ? trim($raw) : '';
        if ($value === '' && !$required) {
            $this->clean[$key] = '';
            return $this;
        }
        $digits = preg_replace('/\D/', '', $value) ?? '';
        if ($value === '') {
            $this->errors[$key] = 'Vul je telefoonnummer in, dan kunnen we de proefrit bevestigen.';
        } elseif (!preg_match('/^\+?[0-9 ()\-]{8,25}$/', $value) || strlen($digits) < 9 || strlen($digits) > 15) {
            $this->errors[$key] = 'Dit telefoonnummer klopt niet. Gebruik bijvoorbeeld 06 12345678.';
        }
        $this->clean[$key] = $value;

        return $this;
    }

    public function accepted(string $key, string $message): self
    {
        if (($this->input[$key] ?? '') !== '1') {
            $this->errors[$key] = $message;
        }

        return $this;
    }

    public function addError(string $key, string $message): self
    {
        $this->errors[$key] = $message;

        return $this;
    }

    public function set(string $key, mixed $value): self
    {
        $this->clean[$key] = $value;

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function get(string $key): mixed
    {
        return $this->clean[$key] ?? null;
    }

    public function old(): array
    {
        $old = [];
        foreach ($this->input as $key => $value) {
            if (is_string($value) && !in_array($key, ['_token', '_ts', 'website'], true)) {
                $old[$key] = mb_substr($value, 0, 2000);
            }
        }

        return $old;
    }
}
