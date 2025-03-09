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

    'accepted'             => 'Pole :attribute musi zostać zaakceptowane.',
    'accepted_if'          => 'Pole :attribute musi zostać zaakceptowane, gdy :other jest :value.',
    'active_url'           => 'Pole :attribute musi być prawidłowym adresem URL.',
    'after'                => 'Pole :attribute musi być datą późniejszą niż :date.',
    'after_or_equal'       => 'Pole :attribute musi być datą późniejszą lub równą :date.',
    'alpha'                => 'Pole :attribute może zawierać tylko litery.',
    'alpha_dash'           => 'Pole :attribute może zawierać tylko litery, cyfry, myślniki i podkreślenia.',
    'alpha_num'            => 'Pole :attribute może zawierać tylko litery i cyfry.',
    'array'                => 'Pole :attribute musi być tablicą.',
    'ascii'                => 'Pole :attribute może zawierać tylko jednobajtowe znaki alfanumeryczne i symbole.',
    'before'               => 'Pole :attribute musi być datą wcześniejszą niż :date.',
    'before_or_equal'      => 'Pole :attribute musi być datą wcześniejszą lub równą :date.',
    'between'              => [
        'array'   => 'Pole :attribute musi mieć od :min do :max elementów.',
        'file'    => 'Pole :attribute musi mieć rozmiar między :min a :max kilobajtów.',
        'numeric' => 'Pole :attribute musi zawierać wartość między :min a :max.',
        'string'  => 'Pole :attribute musi mieć od :min do :max znaków.',
    ],
    'boolean'              => 'Pole :attribute musi mieć wartość true lub false.',
    'can'                  => 'Pole :attribute zawiera nieautoryzowaną wartość.',
    'confirmed'            => 'Potwierdzenie pola :attribute nie zgadza się.',
    'contains'             => 'Pole :attribute nie zawiera wymaganej wartości.',
    'current_password'     => 'Podane hasło jest nieprawidłowe.',
    'date'                 => 'Pole :attribute musi być poprawną datą.',
    'date_equals'          => 'Pole :attribute musi być datą równą :date.',
    'date_format'          => 'Pole :attribute musi odpowiadać formatowi :format.',
    'decimal'              => 'Pole :attribute musi mieć :decimal miejsc dziesiętnych.',
    'declined'             => 'Pole :attribute musi zostać odrzucone.',
    'declined_if'          => 'Pole :attribute musi zostać odrzucone, gdy :other jest :value.',
    'different'            => 'Pole :attribute i :other muszą się różnić.',
    'digits'               => 'Pole :attribute musi składać się z :digits cyfr.',
    'digits_between'       => 'Pole :attribute musi składać się z od :min do :max cyfr.',
    'dimensions'           => 'Pole :attribute ma nieprawidłowe wymiary obrazu.',
    'distinct'             => 'Pole :attribute ma zduplikowaną wartość.',
    'doesnt_end_with'      => 'Pole :attribute nie może kończyć się jednym z następujących: :values.',
    'doesnt_start_with'    => 'Pole :attribute nie może zaczynać się od któregoś z następujących: :values.',
    'email'                => 'Pole :attribute musi być prawidłowym adresem e-mail.',
    'ends_with'            => 'Pole :attribute musi kończyć się jednym z następujących: :values.',
    'enum'                 => 'Wybrany :attribute jest nieprawidłowy.',
    'exists'               => 'Wybrany :attribute jest nieprawidłowy.',
    'extensions'           => 'Pole :attribute musi mieć jedno z następujących rozszerzeń: :values.',
    'file'                 => 'Pole :attribute musi być plikiem.',
    'filled'               => 'Pole :attribute nie może być puste.',
    'gt'                   => [
        'array'   => 'Pole :attribute musi zawierać więcej niż :value elementów.',
        'file'    => 'Rozmiar pola :attribute musi być większy niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'string'  => 'Pole :attribute musi zawierać więcej niż :value znaków.',
    ],
    'gte'                  => [
        'array'   => 'Pole :attribute musi zawierać co najmniej :value elementów.',
        'file'    => 'Pole :attribute musi mieć rozmiar co najmniej :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'string'  => 'Pole :attribute musi zawierać co najmniej :value znaków.',
    ],
    'hex_color'            => 'Pole :attribute musi być prawidłowym kolorem w formacie szesnastkowym.',
    'image'                => 'Pole :attribute musi być obrazem.',
    'in'                   => 'Wybrany :attribute jest nieprawidłowy.',
    'in_array'             => 'Pole :attribute musi istnieć w :other.',
    'integer'              => 'Pole :attribute musi być liczbą całkowitą.',
    'ip'                   => 'Pole :attribute musi być prawidłowym adresem IP.',
    'ipv4'                 => 'Pole :attribute musi być prawidłowym adresem IPv4.',
    'ipv6'                 => 'Pole :attribute musi być prawidłowym adresem IPv6.',
    'json'                 => 'Pole :attribute musi być prawidłowym ciągiem JSON.',
    'list'                 => 'Pole :attribute musi być listą.',
    'lowercase'            => 'Pole :attribute musi być zapisane małymi literami.',
    'lt'                   => [
        'array'   => 'Pole :attribute musi zawierać mniej niż :value elementów.',
        'file'    => 'Pole :attribute musi być mniejsze niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'string'  => 'Pole :attribute musi zawierać mniej niż :value znaków.',
    ],
    'lte'                  => [
        'array'   => 'Pole :attribute nie może zawierać więcej niż :value elementów.',
        'file'    => 'Pole :attribute musi mieć rozmiar nie większy niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'string'  => 'Pole :attribute musi zawierać nie więcej niż :value znaków.',
    ],
    'mac_address'          => 'Pole :attribute musi być prawidłowym adresem MAC.',
    'max'                  => [
        'array'   => 'Pole :attribute nie może zawierać więcej niż :max elementów.',
        'file'    => 'Pole :attribute nie może być większe niż :max kilobajtów.',
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'string'  => 'Pole :attribute nie może zawierać więcej niż :max znaków.',
    ],
    'max_digits'           => 'Pole :attribute nie może mieć więcej niż :max cyfr.',
    'mimes'                => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes'            => 'Pole :attribute musi być plikiem typu: :values.',
    'min'                  => [
        'array'   => 'Pole :attribute musi zawierać co najmniej :min elementów.',
        'file'    => 'Pole :attribute musi mieć co najmniej :min kilobajtów.',
        'numeric' => 'Pole :attribute musi być co najmniej :min.',
        'string'  => 'Pole :attribute musi zawierać co najmniej :min znaków.',
    ],
    'min_digits'           => 'Pole :attribute musi zawierać co najmniej :min cyfr.',
    'missing'              => 'Pole :attribute musi być pominięte.',
    'missing_if'           => 'Pole :attribute musi być pominięte, gdy :other jest :value.',
    'missing_unless'       => 'Pole :attribute musi być pominięte, chyba że :other jest :value.',
    'missing_with'         => 'Pole :attribute musi być pominięte, gdy :values jest obecne.',
    'missing_with_all'     => 'Pole :attribute musi być pominięte, gdy :values są obecne.',
    'multiple_of'          => 'Pole :attribute musi być wielokrotnością :value.',
    'not_in'               => 'Wybrany :attribute jest nieprawidłowy.',
    'not_regex'            => 'Format pola :attribute jest nieprawidłowy.',
    'numeric'              => 'Pole :attribute musi być liczbą.',
    'password'             => [
        'letters'       => 'Pole :attribute musi zawierać przynajmniej jedną literę.',
        'mixed'         => 'Pole :attribute musi zawierać przynajmniej jedną wielką i jedną małą literę.',
        'numbers'       => 'Pole :attribute musi zawierać przynajmniej jedną cyfrę.',
        'symbols'       => 'Pole :attribute musi zawierać przynajmniej jeden symbol.',
        'uncompromised' => 'Podany :attribute pojawił się w wycieku danych. Proszę wybrać inny :attribute.',
    ],
    'present'              => 'Pole :attribute musi być obecne.',
    'present_if'           => 'Pole :attribute musi być obecne, gdy :other jest :value.',
    'present_unless'       => 'Pole :attribute musi być obecne, chyba że :other jest :value.',
    'present_with'         => 'Pole :attribute musi być obecne, gdy :values jest obecne.',
    'present_with_all'     => 'Pole :attribute musi być obecne, gdy :values są obecne.',
    'prohibited'           => 'Pole :attribute jest zabronione.',
    'prohibited_if'        => 'Pole :attribute jest zabronione, gdy :other jest :value.',
    'prohibited_if_accepted'   => 'Pole :attribute jest zabronione, gdy :other jest zaakceptowane.',
    'prohibited_if_declined'   => 'Pole :attribute jest zabronione, gdy :other jest odrzucone.',
    'prohibited_unless'    => 'Pole :attribute jest zabronione, chyba że :other znajduje się w :values.',
    'prohibits'            => 'Pole :attribute zabrania obecności :other.',
    'regex'                => 'Format pola :attribute jest nieprawidłowy.',
    'required'             => 'Pole :attribute jest wymagane.',
    'required_array_keys'  => 'Pole :attribute musi zawierać wpisy dla: :values.',
    'required_if'          => 'Pole :attribute jest wymagane, gdy :other jest :value.',
    'required_if_accepted' => 'Pole :attribute jest wymagane, gdy :other jest zaakceptowane.',
    'required_if_declined' => 'Pole :attribute jest wymagane, gdy :other jest odrzucone.',
    'required_unless'      => 'Pole :attribute jest wymagane, chyba że :other znajduje się w :values.',
    'required_with'        => 'Pole :attribute jest wymagane, gdy :values jest obecne.',
    'required_with_all'    => 'Pole :attribute jest wymagane, gdy :values są obecne.',
    'required_without'     => 'Pole :attribute jest wymagane, gdy :values nie jest obecne.',
    'required_without_all' => 'Pole :attribute jest wymagane, gdy żadne z :values nie są obecne.',
    'same'                 => 'Pole :attribute musi być zgodne z :other.',
    'size'                 => [
        'array'   => 'Pole :attribute musi zawierać :size elementów.',
        'file'    => 'Pole :attribute musi mieć :size kilobajtów.',
        'numeric' => 'Pole :attribute musi mieć wartość :size.',
        'string'  => 'Pole :attribute musi zawierać :size znaków.',
    ],
    'starts_with'          => 'Pole :attribute musi zaczynać się od jednego z następujących: :values.',
    'string'               => 'Pole :attribute musi być ciągiem znaków.',
    'timezone'             => 'Pole :attribute musi być prawidłową strefą czasową.',
    'unique'               => 'Pole :attribute jest już zajęte.',
    'uploaded'             => 'Nie udało się przesłać pola :attribute.',
    'uppercase'            => 'Pole :attribute musi być zapisane wielkimi literami.',
    'url'                  => 'Pole :attribute musi być prawidłowym adresem URL.',
    'ulid'                 => 'Pole :attribute musi być prawidłowym ULID.',
    'uuid'                 => 'Pole :attribute musi być prawidłowym UUID.',

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
