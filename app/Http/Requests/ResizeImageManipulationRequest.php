<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ResizeImageManipulationRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image' => ['required'],
            'w' => ['required','regex:/^\d+(\.\d+)?%?$/'], // 50, 50%, 50.23, 50.213%
            'h' => ['regex:/^\d+(\.\d+)?%?$/'],
            'album_id' => ['exists:\App\Models\Album,id']
        ];
        $image = $this->all()['image'];

        if ($image && $image instanceof UploadedFile) {
            $rules['image'][] = 'image';
        } else {
            $rules['image'] = 'url';
        }
        return $rules;
    }
}
