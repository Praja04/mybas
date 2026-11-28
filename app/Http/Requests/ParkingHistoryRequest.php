<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParkingHistoryRequest extends FormRequest
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
        return [
            'nik'     => 'required|string|max:15',
            'sn_card' => 'required|string',
            'nama'    => 'required|string',
            // 'tapped_at' => 'required|date',
            'status'  => 'required|in:IN,OUT',
        ];
    }

    public function messages()
    {
        return [
            'nik.required'     => 'NIK Wajib Diisi',
            'sn_card.required' => 'Nomor Kartu Wajib Diisi',
            'nama.required'    => 'Nama Wajib Diisi',
            // 'tapped_at.required' => 'Waktu Tap Wajib Diisi',
            'status.required'  => 'Status Wajib Diisi',
        ];
    }
}
