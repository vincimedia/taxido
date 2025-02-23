<?php

return array(
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

    'accepted' => 'يجب قبول :attribute.',
    'active_url' => 'رابط :attribute غير صالح.',
    'after' => 'يجب أن يكون :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخًا بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي :attribute على حروف فقط.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على حروف، أرقام، شرطات، وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروف وأرقام فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخًا قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخًا قبل أو يساوي :date.',
    'between' => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على :min إلى :max حروف.',
        'array' => 'يجب أن يحتوي :attribute على :min إلى :max عناصر.',
    ],
    'boolean' => 'يجب أن يكون حقل :attribute إما صحيحًا أو خطأ.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'التاريخ في :attribute غير صالح.',
    'date_equals' => 'يجب أن يكون :attribute تاريخًا يساوي :date.',
    'date_format' => 'التاريخ في :attribute لا يتطابق مع التنسيق :format.',
    'different' => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون :attribute مكونًا من :digits أرقام.',
    'digits_between' => 'يجب أن يكون :attribute بين :min و :max أرقام.',
    'dimensions' => 'الأبعاد في :attribute غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'exists' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'file' => 'يجب أن يكون :attribute ملفًا.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على أكثر من :value حروف.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من أو يساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على :value حروف أو أكثر.',
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صالح.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالح.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالح.',
    'json' => 'يجب أن يكون :attribute سلسلة JSON صالحة.',
    'lt' => [
        'numeric' => 'يجب أن يكون :attribute أقل من :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على أقل من :value حروف.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على أقل من أو يساوي :value حروف.',
        'array' => 'يجب أن يحتوي :attribute على أقل من أو يساوي :value عناصر.',
    ],
    'max' => [
        'numeric' => 'يجب أن لا يكون :attribute أكبر من :max.',
        'file' => 'يجب أن لا يكون حجم :attribute أكبر من :max كيلوبايت.',
        'string' => 'يجب أن لا يحتوي :attribute على أكثر من :max حروف.',
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفًا من نوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفًا من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على الأقل :min حروف.',
        'array' => 'يجب أن يحتوي :attribute على الأقل :min عناصر.',
    ],
    'multiple_of' => 'يجب أن يكون :attribute مضاعفًا لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'not_regex' => 'تنسيق :attribute غير صالح.',
    'numeric' => 'يجب أن يكون :attribute عددًا.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute موجودًا.',
    'regex' => 'تنسيق :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا تكون :values موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute محظور إلا إذا كان :other في :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'numeric' => 'يجب أن يكون :attribute :size.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على :size حروف.',
        'array' => 'يجب أن يحتوي :attribute على :size عناصر.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute سلسلة نصية.',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صالحة.',
    'unique' => 'تم أخذ :attribute بالفعل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'url' => 'تنسيق :attribute غير صالح.',
    'uuid' => 'يجب أن يكون :attribute UUID صالح.',
    'address_type_in' => 'نوع العنوان يجب أن يكون إما شحن أو فواتير.',
    'name_field_required' => 'حقل الاسم مطلوب.',
    'email_field_required' => 'حقل البريد الإلكتروني مطلوب.',
    'email_already_taken' => 'تم أخذ البريد الإلكتروني بالفعل.',
    'phone_field_required' => 'حقل الهاتف مطلوب.',
    'phone_already_taken' => 'تم أخذ الهاتف بالفعل.',
    'phone_digits_between' => 'عدد أرقام الهاتف يجب أن يكون بين 9 إلى 15.',
    'password_field_required' => 'حقل كلمة المرور مطلوب.',
    'status_field_required' => 'حقل الحالة مطلوب.',
    'username_field_required' => 'حقل اسم المستخدم مطلوب.',
    'slug_field_required' => 'حقل السلاug مطلوب.',
    'slug_unique' => 'يجب أن يكون السلاug فريدًا.',
    'content_field_required' => 'حقل المحتوى مطلوب.',
    'status_invalid' => 'الحالة غير صالحة. يجب أن تكون إما 0 أو 1.',
    'created_by_exists' => 'المستخدم الذي أنشأ هذه الصفحة غير موجود.',
    'meta_image_invalid_url' => 'صورة الميتا يجب أن تكون رابطًا صالحًا.',
    'slug_invalid' => 'تنسيق السلاug غير صالح.',
    'meta_image_invalid' => 'صورة الميتا المقدمة غير صالحة أو لا يمكن معالجتها.',
    'blog_thumbnail.url' => 'صورة المدونة المصغرة يجب أن تكون رابطًا صالحًا.',
    'blog_meta_image.url' => 'صورة الميتا للمدونة يجب أن تكون رابطًا صالحًا.',
    'file_name_required' => 'حقل اسم الملف مطلوب.',
    'model_id_exists' => 'معرف النموذج المحدد غير موجود.',
);