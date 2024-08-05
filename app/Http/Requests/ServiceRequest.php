<?php

namespace App\Http\Requests;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'=>'nullable|exists:services,id',
            'name_ar'           => 'required|max:255|string',
            'name_en'           => 'required|max:255|string',
            'description_ar'    => 'required|string',
            'description_en'    => 'required|string',
            'status'            => 'nullable|boolean',
            'price'             => 'nullable|numeric',
            'discount'          => 'nullable|numeric',
            'image'             => 'image|mimes:jpeg,png,jpg|max:6502|required_if:id,null',
            'multiImages.*'     => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id'       => 'required|exists:categories,id',
            'type'              => 'boolean|required'
        ];
    }

    public function messages()
{
    return [
        'name_ar.required'          => 'حقل الاسم بالعربية مطلوب.',
        'name_ar.max'               => 'حقل الاسم بالعربية يجب أن يكون أقل من :max حرف.',
        'name_ar.string'            => 'حقل الاسم بالعربية يجب أن يكون نصًا.',

        'name_en.required'          => 'حقل الاسم بالإنجليزية مطلوب.',
        'name_en.max'               => 'حقل الاسم بالإنجليزية يجب أن يكون أقل من :max حرف.',
        'name_en.string'            => 'حقل الاسم بالإنجليزية يجب أن يكون نصًا.',

        'description_ar.required'   => 'حقل الوصف بالعربية مطلوب.',
        'description_ar.string'     => 'حقل الوصف بالعربية يجب أن يكون نصًا.',

        'description_en.required'   => 'حقل الوصف بالإنجليزية مطلوب.',
        'description_en.string'     => 'حقل الوصف بالإنجليزية يجب أن يكون نصًا.',

        'status.boolean'            => 'حقل الحالة يجب أن يكون صحيحًا أو خطأ.',

        'image.image'               => 'يجب أن يكون الملف عبارة عن صورة.',
        'image.mimes'               => 'يجب أن يكون الملف من نوع: jpeg، png، jpg.',
        'image.max'                 => 'يجب أن يكون حجم الصورة أقل من :max كيلوبايت.',
        'image.required_if'         => 'حقل الصورة مطلوب إذا كانت هذه عملية إنشاء جديدة.',

        'multiImages.*.image'       => 'يجب أن تكون جميع الملفات عبارة عن صور.',
        'multiImages.*.mimes'       => 'يجب أن تكون جميع الملفات من نوع: jpeg، png، jpg، gif.',
        'multiImages.*.max'         => 'يجب أن يكون حجم الصور أقل من :max كيلوبايت.',

        'category_id.required'      => 'حقل فئة الخدمة مطلوب.',
        'category_id.exists'        => 'الفئة المحددة غير موجودة.',

        'type.boolean'              => 'قيمة خاظئه',
        'type.required'              => 'يجب اختيار نوع الخدمة',
    ];
}


protected function failedValidation(Validator $validator)
{
    $errorMessages = $validator->errors()->all();

    // Display each error message with Toastr
    foreach ($errorMessages as $errorMessage) {
        Toastr::error($errorMessage,'Error');
    }

    parent::failedValidation($validator);
}

}
