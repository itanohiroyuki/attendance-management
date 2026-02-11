<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'break1_start_time',
        'break1_end_time',
        'break2_start_time',
        'break2_end_time',
        'work_minutes',
        'note',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'work_date' => 'date',
    ];

    public static function existsTodayForUser(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereDate('work_date', today())
            ->exists();
    }

    public static function todayForUser(int $userId): ?self
    {
        return self::where('user_id', $userId)->whereDate('work_date', today())->first();
    }

    public static function workingTodayForUser(int $userId): self
    {
        return self::where('user_id', $userId)
            ->whereDate('work_date', today())
            ->whereNull('end_time')
            ->firstOrFail();
    }

    public function isWorking(): bool
    {
        return $this->start_time !== null && $this->end_time === null;
    }

    public function isOnBreak(): bool
    {
        if ($this->break1_start_time && !$this->break1_end_time) {
            return true;
        }
        if ($this->break2_start_time && !$this->break2_end_time) {
            return true;
        }
        return false;
    }

    public function isFinished(): bool
    {
        return $this->end_time !== null;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isFinished()) {
            return '退勤済み';
        }
        if ($this->isOnBreak()) {
            return '休憩中';
        }
        if ($this->isWorking()) {
            return '出勤中';
        }
        return '勤務外';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
