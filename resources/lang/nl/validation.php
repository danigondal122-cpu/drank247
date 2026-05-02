<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Het :attribute must be accepted.',
    'active_url' => 'Het :attribute is not a valid URL.',
    'after' => 'Het :attribute must be a date after :date.',
    'after_or_equal' => 'Het :attribute must be a date after or equal to :date.',
    'alpha' => 'Het :attribute may only contain letters.',
    'alpha_dash' => 'Het :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'Het :attribute may only contain letters and numbers.',
    'array' => 'Het :attribute must be an array.',
    'before' => 'Het :attribute must be a date before :date.',
    'before_or_equal' => 'Het :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'Het :attribute must be between :min and :max.',
        'file' => 'Het :attribute must be between :min and :max kilobytes.',
        'string' => 'Het :attribute must be between :min and :max characters.',
        'array' => 'Het :attribute must have between :min and :max items.',
    ],
    'boolean' => 'Het :attribute field must be true or false.',
    'confirmed' => 'Het :attribute confirmation does not match.',
    'date' => 'Het :attribute is geen geldige datum.',
    'date_equals' => 'Het :attribute moet een datum zijn die gelijk is aan :date.',
    'date_format' => 'Het :attribute komt niet overeen met Het formaat :format.',
    'different' => 'Het :attribute and :other moet anders zijn.',
    'digits' => 'Het :attribute moet zijn :digits cijfers.',
    'digits_between' => 'Het :attribute must be between :min and :max digits.',
    'dimensions' => 'Het :attribute has invalid image dimensions.',
    'distinct' => 'Het :attribute field has a duplicate value.',
    'email' => 'Het :attribute Moet een geldig e-mail adres zijn.',
    'ends_with' => 'Het :attribute must end with one of Het following: :values.',
    'exists' => 'Het geselecteerd :attribute is ongeldig.',
    'file' => 'Het :attribute moet een bestand zijn.',
    'filled' => 'Het :attribute veld moet een waarde hebben.',
    'gt' => [
        'numeric' => 'Het :attribute moet groter zijn dan :value.',
        'file' => 'Het :attribute moet groter zijn dan:value kilobytes.',
        'string' => 'Het :attribute moet groter zijn dan:value characters.',
        'array' => 'Het :attribute moet meer hebben dan :value items.',
    ],
    'gte' => [
        'numeric' => 'Het :attribute moet groter zijn dan of gelijk zijn :value.',
        'file' => 'Het :attribute moet groter zijn dan of gelijk zijn :value kilobytes.',
        'string' => 'Het :attribute moet groter zijn dan of gelijk zijn :value characters.',
        'array' => 'Het :attribute hebbeding :value items of meer.',
    ],
    'image' => 'Het :attribute moet een afbeelding zijn.',
    'in' => 'Het geselecteerd :attribute is ongeldig.',
    'in_array' => 'Het :attribute field does not exist in :other.',
    'integer' => 'Het :attribute must be an integer.',
    'ip' => 'Het :attribute must be a valid IP address.',
    'ipv4' => 'Het :attribute must be a valid IPv4 address.',
    'ipv6' => 'Het :attribute must be a valid IPv6 address.',
    'json' => 'Het :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'Het :attribute must be less than :value.',
        'file' => 'Het :attribute must be less than :value kilobytes.',
        'string' => 'Het :attribute must be less than :value characters.',
        'array' => 'Het :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'Het :attribute must be less than or equal :value.',
        'file' => 'Het :attribute must be less than or equal :value kilobytes.',
        'string' => 'Het :attribute must be less than or equal :value characters.',
        'array' => 'Het :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'Het :attribute may not be greater than :max.',
        'file' => 'Het :attribute may not be greater than :max kilobytes.',
        'string' => 'Het :attribute may not be greater than :max characters.',
        'array' => 'Het :attribute may not have more than :max items.',
    ],
    'mimes' => 'Het :attribute must be a file of type: :values.',
    'mimetypes' => 'Het :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'Het :attribute must be at least :min.',
        'file' => 'Het :attribute must be at least :min kilobytes.',
        'string' => 'Het :attribute must be at least :min characters.',
        'array' => 'Het :attribute must have at least :min items.',
    ],
    'not_in' => 'Het selected :attribute is invalid.',
    'not_regex' => 'Het :attribute format is invalid.',
    'numeric' => 'De :attribute moet een getal zijn.',
    'password' => 'Het password is incorrect.',
    'present' => 'Het :attribute field must be present.',
    'regex' => 'Het :attribute format is invalid.',
    'required' => 'Het :attribute veld is verplicht.',
    'required_if' => 'Het :attribute veld is vereist wanneer :other is :value.',
    'required_unless' => 'Het :attribute field is required unless :other is in :values.',
    'required_with' => 'Het :attribute field is required when :values is present.',
    'required_with_all' => 'Het :attribute field is required when :values are present.',
    'required_without' => 'Het :attribute field is required when :values is not present.',
    'required_without_all' => 'Het :attribute field is required when none of :values are present.',
    'same' => 'Het :attribute and :other must match.',
    'size' => [
        'numeric' => 'Het :attribute must be :size.',
        'file' => 'Het :attribute must be :size kilobytes.',
        'string' => 'Het :attribute must be :size characters.',
        'array' => 'Het :attribute must contain :size items.',
    ],
    'starts_with' => 'Het :attribute must start with one of Het following: :values.',
    'string' => 'Het :attribute must be a string.',
    'timezone' => 'Het :attribute must be a valid zone.',
    'unique' => 'De :attribute bestaat al',
    'uploaded' => 'Het :attribute failed to upload.',
    'url' => 'Het :attribute format is invalid.',
    'uuid' => 'Het :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
