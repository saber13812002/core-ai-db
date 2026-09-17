<?php

namespace App\Http\Requests\Api\V1\Concerns;

/**
 * Turns the parent Store request rules into Update rules:
 * every field becomes optional (nullable) and uniqueness constraints are dropped.
 */
trait MakesRulesOptional
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach ($rules as $key => $value) {
            $rules[$key] = $this->makeRuleOptional($value);
        }

        return $rules;
    }

    protected function makeRuleOptional(array|string $rule): array|string
    {
        $rules = is_string($rule) ? explode('|', $rule) : $rule;

        $rules = array_values(array_filter(
            $rules,
            fn ($r) => ! (is_string($r) && in_array($r, ['required', 'present', 'required_if', 'required_unless', 'required_with', 'required_without'], true))
                    && ! (is_string($r) && str_starts_with($r, 'unique:')),
        ));

        if (! in_array('nullable', $rules, true)) {
            array_unshift($rules, 'nullable');
        }

        return count($rules) === 1 && is_string($rules[0]) ? $rules[0] : $rules;
    }
}
