<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time'   => ['nullable', 'date_format:H:i'],
            'end_time'     => ['nullable', 'date_format:H:i'],

            'break1_start_time' => ['nullable', 'date_format:H:i'],
            'break1_end_time'   => ['nullable', 'date_format:H:i'],

            'break2_start_time' => ['nullable', 'date_format:H:i'],
            'break2_end_time'   => ['nullable', 'date_format:H:i'],

            'reason'       => ['required'],
        ];
    }

    public function messages()
    {
        return [
            '*.date_format'   => '時間は HH:MM 形式で入力してください（例：09:00）',
            'reason.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $start = $this->start_time;
            $end   = $this->end_time;

            // 出勤・退勤の前後関係
            if ($start && $end && $start >= $end) {
                $validator->errors()->add(
                    'start_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // 休憩開始が勤務時間外
            foreach (['break1_start_time', 'break2_start_time'] as $breakStart) {
                $breakStartTime = $this->$breakStart;

                if (!$breakStartTime) {
                    continue;
                }

                if (
                    ($start && $breakStartTime < $start) ||
                    ($end && $breakStartTime > $end)
                ) {
                    $validator->errors()->add(
                        $breakStart,
                        '休憩時間が不適切な値です'
                    );
                }
            }

            // 休憩終了が退勤より後
            foreach (['break1_end_time', 'break2_end_time'] as $breakEnd) {
                $breakEndTime = $this->$breakEnd;

                if (!$breakEndTime) {
                    continue;
                }

                if ($end && $breakEndTime > $end) {
                    $validator->errors()->add(
                        $breakEnd,
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
