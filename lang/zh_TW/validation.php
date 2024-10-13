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

    'accepted' => '必須接受 :attribute。',
    'accepted_if' => '當 :other 為 :value 時，:attribute 必須接受。',
    'active_url' => ':attribute 不是有效的網址。',
    'after' => ':attribute 必須要晚於 :date。',
    'after_or_equal' => ':attribute 必須要等於 :date 或更晚。',
    'alpha' => ':attribute 只能以字母組成。',
    'alpha_dash' => ':attribute 只能以字母、數字、連接線(-)及底線(_)組成。',
    'alpha_num' => ':attribute 只能以字母及數字組成。',
    'array' => ':attribute 必須為陣列。',
    'ascii' => ':attribute 必須只包含單字節字母數字字符和符號。',
    'before' => ':attribute 必須要早於 :date。',
    'before_or_equal' => ':attribute 必須要等於 :date 或更早。',
    'between' => [
        'array' => ':attribute 必須有 :min ~ :max 個元素。',
        'file' => ':attribute 必須介於 :min 至 :max KB 之間。 ',
        'numeric' => ':attribute 必須介於 :min 至 :max 之間。',
        'string' => ':attribute 必須介於 :min 至 :max 個字元之間。',
    ],
    'boolean' => ':attribute 必須為布林值。',
    'can' => ':attribute 欄位包含未經授權的值。',
    'confirmed' => ':attribute 確認欄位的輸入不一致。',
    'contains' => ':attribute 欄位缺少必要的值。',
    'current_password' => '當前密碼不正確。',
    'date' => ':attribute 不是有效的日期。',
    'date_equals' => ':attribute 必須等於 :date。',
    'date_format' => ':attribute 不符合 :format 的格式。',
    'decimal' => ':attribute 必須有 :decimal 位小數。',
    'declined' => ':attribute 必須拒絕。',
    'declined_if' => '當 :other 為 :value 時，:attribute 必須拒絕。',
    'different' => ':attribute 與 :other 必須不同。',
    'digits' => ':attribute 必須是 :digits 位數字。',
    'digits_between' => ':attribute 必須介於 :min ~ :max 位數字。',
    'dimensions' => ':attribute 圖片尺寸不正確。',
    'distinct' => ':attribute 已經存在。',
    'doesnt_end_with' => ':attribute 結尾不能包含下列之一：:values。',
    'doesnt_start_with' => ':attribute 開頭不能包含下列之一：:values。',
    'email' => ':attribute 必須是有效的 E-mail。',
    'ends_with' => ':attribute 結尾必須包含下列之一：:values。',
    'enum' => ':attribute 的值不正確。',
    'exists' => ':attribute 不存在。',
    'extensions' => ':attribute 欄位必須具有以下副檔名之一：:values。',
    'file' => ':attribute 必須是有效的檔案。',
    'filled' => ':attribute 不能留空。',
    'gt' => [
        'array' => ':attribute 必須多於 :value 個元素。',
        'file' => ':attribute 必須大於 :value KB。',
        'numeric' => ':attribute 必須大於 :value。',
        'string' => ':attribute 必須多於 :value 個字元。',
    ],
    'gte' => [
        'array' => ':attribute 必須多於或等於 :value 個元素。',
        'file' => ':attribute 必須大於或等於 :value KB。',
        'numeric' => ':attribute 必須大於或等於 :value。',
        'string' => ':attribute 必須多於或等於 :value 個字元。',
    ],
    'hex_color' => ':attribute 欄位必須是有效的十六進位顏色。',
    'image' => ':attribute 必須是一張圖片。',
    'in' => '所選擇的 :attribute 選項無效。',
    'in_array' => ':attribute 沒有在 :other 中。',
    'integer' => ':attribute 必須是一個整數。',
    'ip' => ':attribute 必須是一個有效的 IP 位址。',
    'ipv4' => ':attribute 必須是一個有效的 IPv4 位址。',
    'ipv6' => ':attribute 必須是一個有效的 IPv6 位址。',
    'json' => ':attribute 必須是正確的 JSON 字串。',
    'list' => ':attribute 欄位必須是列表。',
    'lowercase' => ':attribute 必須是小寫的。',
    'lt' => [
        'array' => ':attribute 必須少於 :value 個元素。',
        'file' => ':attribute 必須小於 :value KB。',
        'numeric' => ':attribute 必須小於 :value。',
        'string' => ':attribute 必須少於 :value 個字元。',
    ],
    'lte' => [
        'array' => ':attribute 必須少於或等於 :value 個元素。',
        'file' => ':attribute 必須小於或等於 :value KB。',
        'numeric' => ':attribute 必須小於或等於 :value。',
        'string' => ':attribute 必須少於或等於 :value 個字元。',
    ],
    'mac_address' => ':attribute 必須是一個有效的 MAC 位址。',
    'max' => [
        'array' => ':attribute 不能多於 :max 個元素。',
        'file' => ':attribute 不能大於 :max KB。',
        'numeric' => ':attribute 不能大於 :max。',
        'string' => ':attribute 不能多於 :max 個字元。',
    ],
    'max_digits' => ':attribute 不能多於 :max 位數字。',
    'mimes' => ':attribute 必須為 :values 的檔案。',
    'mimetypes' => ':attribute 必須為 :values 的檔案。',
    'min' => [
        'array' => ':attribute 不能少於 :min 個元素。',
        'file' => ':attribute 不能小於 :min KB。',
        'numeric' => ':attribute 不能小於 :min。',
        'string' => ':attribute 不能少於 :min 個字元。',
    ],
    'min_digits' => ':attribute 不能少於 :min 位數字。',
    'missing' => ':attribute 不得存在。',
    'missing_if' => '當 :other 為 :value 時，:attribute 不得存在。',
    'missing_unless' => ':attribute 不得存在，除非 :other 在 :values 中。',
    'missing_with' => '當 :values 出現時 :attribute 不得存在。',
    'missing_with_all' => '當 :values 出現時 :attribute 不得存在。',
    'multiple_of' => '所選擇的 :attribute 必須為 :value 中的多個。',
    'not_in' => '所選擇的 :attribute 選項無效。',
    'not_regex' => ':attribute 的格式錯誤。',
    'numeric' => ':attribute 必須為一個數字。',
    'password' => [
        'letters' => ':attribute 必須至少包含一個字母。',
        'mixed' => ':attribute 必須至少包含一個大寫字母和一個小寫字母。',
        'numbers' => ':attribute 必須包含至少一個數字。',
        'symbols' => ':attribute 必須包含至少一個特殊符號。',
        'uncompromised' => ':attribute 出現資料外洩。 請選擇不同的 :attribute。',
    ],
    'present' => ':attribute 必須存在。',
    'present_if' => '當 :other 等於 :value 時，必須存在 :attribute 個欄位。',
    'present_unless' => '除非 :other 等於 :value，否則 :attribute 個欄位必須存在。',
    'present_with' => '當 :values 存在時，:attribute 個欄位必須存在。',
    'present_with_all' => '當存在 :values 時，必須存在 :attribute 個欄位。',
    'prohibited' => ':attribute 被禁止。',
    'prohibited_if' => '當 :other 為 :value 時，:attribute 被禁止。',
    'prohibited_unless' => ':attribute 被禁止，除非 :other 在 :values 中。',
    'prohibits' => ':attribute 禁止包含 :other。',
    'regex' => ':attribute 的格式錯誤。',
    'required' => ':attribute 不能留空。',
    'required_array_keys' => ':attribute 必須包含 :values 中的一個鍵。',
    'required_if' => '當 :other 為 :value 時 :attribute 不能留空。',
    'required_if_accepted' => '當 :other 被接受時，:attribute 是必需的。',
    'required_if_declined' => '拒絕 :other 時需要 :attribute 欄位。',
    'required_unless' => '當 :other 不為 :values 時 :attribute 不能留空。',
    'required_with' => '當 :values 出現時 :attribute 不能留空。',
    'required_with_all' => '當 :values 出現時 :attribute 不能為空。',
    'required_without' => '當 :values 留空時 :attribute 不能留空。',
    'required_without_all' => '當 :values 都不出現時 :attribute 不能留空。',
    'same' => ':attribute 與 :other 必須相同。',
    'size' => [
        'array' => ':attribute 必須是 :size 個元素。',
        'file' => ':attribute 的大小必須是 :size KB。',
        'numeric' => ':attribute 的大小必須是 :size。',
        'string' => ':attribute 必須是 :size 個字元。',
    ],
    'starts_with' => ':attribute 開頭必須包含下列之一：:values。',
    'string' => ':attribute 必須是一個字串。',
    'timezone' => ':attribute 必須是一個正確的時區值。',
    'unique' => ':attribute 已經存在。',
    'uploaded' => ':attribute 上傳失敗。',
    'uppercase' => ':attribute 必須是大寫的。',
    'url' => ':attribute 必須是有效的網址格式。',
    'ulid' => ':attribute 必須是有效的 ULID。',
    'uuid' => ':attribute 必須是有效的 UUID。',

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
